<?php
// ============================================
// historique.php - UNIQUEMENT LOGIQUE PHP
// Pas de HTML, pas de CSS, juste du PHP
// ============================================

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';
require_once 'verification_role.php';

// Vérification des droits
est_connecte();
verifier_acces(['super_admin', 'admin_regional', 'chef_agence', 'agent'], true);

// Récupération des paramètres de filtre
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$dateDebut = isset($_GET['dateDebut']) ? $_GET['dateDebut'] : '';
$dateFin = isset($_GET['dateFin']) ? $_GET['dateFin'] : '';

$condition_agence = condition_agence('u');
$error = null;
$prets = [];
$paiements = [];
$clients = [];
$statsPrets = ['total' => 0, 'approuves' => 0, 'en_attente' => 0, 'refuses' => 0];
$statsPaiements = ['total' => 0, 'rembourses' => 0, 'en_attente' => 0, 'annules' => 0];
$statsClients = ['total' => 0, 'actifs' => 0, 'retard' => 0];

try {
    // 1. Récupération des prêts
    $sqlPrets = "SELECT dp.*, u.nom, u.prenom, u.agence_id, a.nom as agence_nom 
                 FROM demande_pret dp
                 JOIN users u ON dp.utilisateur_id = u.id
                 LEFT JOIN agences a ON u.agence_id = a.id_agence
                 WHERE $condition_agence";
    $params = [];

    if (!empty($statusFilter)) {
        $sqlPrets .= " AND dp.statut = :status";
        $params[':status'] = $statusFilter;
    }
    if (!empty($dateDebut)) {
        $sqlPrets .= " AND DATE(dp.date_demande) >= :dateDebut";
        $params[':dateDebut'] = $dateDebut;
    }
    if (!empty($dateFin)) {
        $sqlPrets .= " AND DATE(dp.date_demande) <= :dateFin";
        $params[':dateFin'] = $dateFin;
    }

    $sqlPrets .= " ORDER BY dp.date_demande DESC";
    $stmtPrets = $conn->prepare($sqlPrets);
    $stmtPrets->execute($params);
    $prets = $stmtPrets->fetchAll(PDO::FETCH_ASSOC);

    // 2. Récupération des paiements
    $sqlPaiements = "SELECT r.*, u.nom, u.prenom, u.agence_id, a.nom as agence_nom,
                            (SELECT SUM(montant) FROM remboursement WHERE utilisateur_id = r.utilisateur_id AND statut = 'remboursé') as total_rembourse
                     FROM remboursement r
                     JOIN users u ON r.utilisateur_id = u.id
                     LEFT JOIN agences a ON u.agence_id = a.id_agence
                     WHERE $condition_agence
                     GROUP BY r.id
                     ORDER BY r.date_remboursement DESC";
    $stmtPaiements = $conn->query($sqlPaiements);
    $paiements = $stmtPaiements->fetchAll(PDO::FETCH_ASSOC);

    // 3. Récupération des clients
    $sqlClients = "SELECT u.*, a.nom as agence_nom,
                          COUNT(DISTINCT dp.id) as nb_prets,
                          SUM(dp.montant) as montant_total_prets,
                          (SELECT SUM(montant) FROM remboursement WHERE utilisateur_id = u.id AND statut = 'remboursé') as total_rembourse,
                          CASE 
                              WHEN EXISTS (
                                  SELECT 1 FROM remboursement r 
                                  WHERE r.utilisateur_id = u.id 
                                  AND r.statut = 'en attente' 
                                  AND r.date_remboursement < NOW()
                              ) THEN 'En retard'
                              WHEN COUNT(dp.id) > 0 THEN 'Actif'
                              ELSE 'Inactif'
                          END as statut_global
                   FROM users u
                   LEFT JOIN demande_pret dp ON u.id = dp.utilisateur_id
                   LEFT JOIN agences a ON u.agence_id = a.id_agence
                   WHERE u.role = 'client' AND $condition_agence
                   GROUP BY u.id
                   ORDER BY u.nom, u.prenom";
    $stmtClients = $conn->query($sqlClients);
    $clients = $stmtClients->fetchAll(PDO::FETCH_ASSOC);

    // Statistiques
    $statsPrets = [
        'total' => count($prets),
        'approuves' => count(array_filter($prets, fn($p) => $p['statut'] == 'approuvé')),
        'en_attente' => count(array_filter($prets, fn($p) => $p['statut'] == 'en attente')),
        'refuses' => count(array_filter($prets, fn($p) => $p['statut'] == 'refusé'))
    ];

    $statsPaiements = [
        'total' => count($paiements),
        'rembourses' => count(array_filter($paiements, fn($p) => $p['statut'] == 'remboursé')),
        'en_attente' => count(array_filter($paiements, fn($p) => $p['statut'] == 'en attente')),
        'annules' => count(array_filter($paiements, fn($p) => $p['statut'] == 'annulé'))
    ];

    $statsClients = [
        'total' => count($clients),
        'actifs' => count(array_filter($clients, fn($c) => $c['statut_global'] == 'Actif')),
        'retard' => count(array_filter($clients, fn($c) => $c['statut_global'] == 'En retard'))
    ];

    // Journalisation
    journal_action('consultation_historique', "Filtres - Statut: $statusFilter, Dates: $dateDebut - $dateFin");

} catch(PDOException $e) {
    error_log("Erreur historique.php: " . $e->getMessage());
    $error = "Erreur de chargement des données : " . $e->getMessage();
}

$username = $_SESSION['username'] ?? 'Utilisateur';
$role = $_SESSION['role'] ?? 'agent';
$agence_id = $_SESSION['agence_id'] ?? null;

// Récupérer le nom de l'agence si nécessaire
$agence_nom = null;
if ($agence_id) {
    try {
        $stmt = $conn->prepare("SELECT nom FROM agences WHERE id_agence = ?");
        $stmt->execute([$agence_id]);
        $agence = $stmt->fetch();
        $agence_nom = $agence ? $agence['nom'] : null;
    } catch(Exception $e) {}
}

// Inclure la vue (HTML)
include 'pages/historique_content.php';

// charger layout
include("layout.php");
?>