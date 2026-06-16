<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

est_connecte();
verifier_acces(['super_admin', 'admin_regional', 'chef_agence', 'agent'], true);

$search = $_GET['search'] ?? '';
$condition_agence = condition_agence('u');


/* ======================
   CHANGEMENT DE STATUT
====================== */

if ($_SERVER['REQUEST_METHOD'] == 'POST'
    && isset($_POST['id'])
    && isset($_POST['statut'])) {

    $id = intval($_POST['id']);
    $statut = $_POST['statut'];
    $motif_refus = $_POST['motif_refus'] ?? null;

    $statuts_valides = ['approuvé', 'refusé'];

    if ($statut == 'refusé') {

        $sqlUpdate = "
            UPDATE demande_pret
            SET statut = :statut,
                motif_refus = :motif_refus
            WHERE id = :id
        ";
    
        $stmtUpdate = $conn->prepare($sqlUpdate);
    
        $stmtUpdate->execute([
            'statut' => $statut,
            'motif_refus' => $motif_refus,
            'id' => $id
        ]);
    
    } else {
    
        $sqlUpdate = "
            UPDATE demande_pret
            SET statut = :statut
            WHERE id = :id
        ";
    
        $stmtUpdate = $conn->prepare($sqlUpdate);
    
        $stmtUpdate->execute([
            'statut' => $statut,
            'id' => $id
        ]);
    }
        
        // Si la demande est approuvée, créer un remboursement
        if ($statut == 'approuvé') {
        
            // Récupérer les informations de la demande
            $sqlDemande = "
            SELECT utilisateur_id,
                   montant,
                   type_pret,
                   motif,
                   duree
            FROM demande_pret
            WHERE id = :id
        ";
        
            $stmtDemande = $conn->prepare($sqlDemande);
            $stmtDemande->execute(['id' => $id]);
        
            $demande = $stmtDemande->fetch(PDO::FETCH_ASSOC);
        
            if ($demande) {
        
                // Vérifier qu'un remboursement n'existe pas déjà
                $check = $conn->prepare("
                    SELECT COUNT(*) 
                    FROM remboursement
                    WHERE utilisateur_id = :utilisateur_id
                    AND montant = :montant
                ");
        
                $check->execute([
                    'utilisateur_id' => $demande['utilisateur_id'],
                    'montant' => $demande['montant']
                ]);
        
                if ($check->fetchColumn() == 0) {

                    // Création du remboursement
                    $insert = $conn->prepare("
                        INSERT INTO remboursement
                        (utilisateur_id, montant, date_remboursement, statut)
                        VALUES
                        (:utilisateur_id, :montant, CURDATE(), 'en attente')
                    ");
                
                    $insert->execute([
                        'utilisateur_id' => $demande['utilisateur_id'],
                        'montant' => $demande['montant']
                    ]);
                
                    // Création de la planification
                    $dateDebut = date('Y-m-d');
                    $dateFin = date('Y-m-d', strtotime("+{$demande['duree']} months"));
                
                    $insertPlanification = $conn->prepare("
                        INSERT INTO planification
                        (
                            utilisateur_id,
                            titre,
                            description,
                            date_debut,
                            date_fin,
                            statut
                        )
                        VALUES
                        (
                            :utilisateur_id,
                            :titre,
                            :description,
                            :date_debut,
                            :date_fin,
                            'en cours'
                        )
                    ");
                
                    $insertPlanification->execute([
                        'utilisateur_id' => $demande['utilisateur_id'],
                        'titre' => $demande['type_pret'],
                        'description' => $demande['motif'],
                        'date_debut' => $dateDebut,
                        'date_fin' => $dateFin
                    ]);
                }
            }
        }
        
        journal_action(
            'modification_statut',
            "Demande #$id => $statut"
        );
    }

try {
    $sql = "SELECT 
    dp.*,
    u.username AS client_nom,
    a.nom AS agence_nom
    FROM demande_pret dp
    JOIN users u ON dp.utilisateur_id = u.id
    LEFT JOIN agences a ON u.agence_id = a.id_agence
    WHERE $condition_agence";
    
    if (!empty($search)) {
        $sql .= " AND (u.nom LIKE :search OR u.prenom LIKE :search OR dp.montant LIKE :search)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['search' => "%$search%"]);
    } else {
        $stmt = $conn->query($sql);
    }
    
    $demandes = $stmt->fetchAll();

    journal_action('consultation_demandes_pret', "Recherche: $search");

} catch(PDOException $e) {
    $error = "Erreur lors du chargement des données.";
}

// très important
$content = "pages/demande_content.php";
include("layout.php");