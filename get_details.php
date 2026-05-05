<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'config.php';
require_once 'verification_role.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit();
}

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$type || !$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètres manquants']);
    exit();
}

try {
    $response = ['title' => '', 'content' => ''];

    switch($type) {
        case 'pret':
            // Détails d'une demande de prêt
            $sql = "SELECT dp.*, u.nom, u.prenom, u.email, u.telephone, u.agence_id, a.nom as agence_nom
                    FROM demande_pret dp
                    JOIN users u ON dp.utilisateur_id = u.id
                    LEFT JOIN agences a ON u.agence_id = a.id_agence
                    WHERE dp.id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $pret = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($pret) {
                $response['title'] = "Demande de prêt #" . $id . " - " . htmlspecialchars($pret['nom'] . ' ' . $pret['prenom']);
                
                // Récupérer l'historique des remboursements pour ce client (SANS agence_id)
                $sqlRemb = "SELECT r.*
                           FROM remboursement r
                           WHERE r.utilisateur_id = :user_id 
                           ORDER BY r.date_remboursement DESC";
                $stmtRemb = $conn->prepare($sqlRemb);
                $stmtRemb->execute([':user_id' => $pret['utilisateur_id']]);
                $remboursements = $stmtRemb->fetchAll(PDO::FETCH_ASSOC);

                $badgeClass = 'secondary';
                switch($pret['statut']) {
                    case 'approuvé': $badgeClass = 'success'; break;
                    case 'en attente': $badgeClass = 'warning'; break;
                    case 'refusé': $badgeClass = 'danger'; break;
                }

                $response['content'] = '
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Informations du prêt</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr><th>ID Prêt:</th><td>' . $pret['id'] . '</td></tr>
                                        <tr><th>Client:</th><td>' . htmlspecialchars($pret['nom'] . ' ' . $pret['prenom']) . '</td></tr>
                                        <tr><th>Montant:</th><td class="text-success font-weight-bold">' . number_format($pret['montant'], 0, ',', ' ') . ' Ar</td></tr>
                                        <tr><th>Durée:</th><td>' . $pret['duree'] . ' mois</td></tr>
                                        <tr><th>Taux:</th><td>' . $pret['taux_interet'] . '%</td></tr>
                                        <tr><th>Statut:</th><td><span class="badge badge-' . $badgeClass . '">' . ucfirst($pret['statut']) . '</span></td></tr>
                                        <tr><th>Date demande:</th><td>' . date('d/m/Y H:i', strtotime($pret['date_demande'])) . '</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-info card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Informations client</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr><th>ID Client:</th><td>' . $pret['utilisateur_id'] . '</td></tr>
                                        <tr><th>Nom complet:</th><td>' . htmlspecialchars($pret['nom'] . ' ' . $pret['prenom']) . '</td></tr>
                                        <tr><th>Email:</th><td>' . htmlspecialchars($pret['email']) . '</td></tr>
                                        <tr><th>Téléphone:</th><td>' . htmlspecialchars($pret['telephone'] ?? '-') . '</td></tr>
                                        <tr><th>Agence:</th><td>' . htmlspecialchars($pret['agence_nom'] ?? '-') . '</td></tr>
                                    </table>
                                </div>
                            </div>';

                if (!empty($remboursements)) {
                    $response['content'] .= '
                            <div class="card card-success card-outline mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">Historique des remboursements</h5>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm">
                                        <thead><tr><th>Date</th><th>Montant</th><th>Statut</th></tr></thead>
                                        <tbody>';
                    foreach ($remboursements as $remb) {
                        $rembBadge = $remb['statut'] == 'remboursé' ? 'success' : ($remb['statut'] == 'en attente' ? 'warning' : 'danger');
                        $response['content'] .= '
                                            <tr>
                                                <td>' . date('d/m/Y', strtotime($remb['date_remboursement'])) . '</td>
                                                <td>' . number_format($remb['montant'], 0, ',', ' ') . ' Ar</td>
                                                <td><span class="badge badge-' . $rembBadge . '">' . ucfirst($remb['statut']) . '</span></td>
                                            </tr>';
                    }
                    $response['content'] .= '
                                        </tbody>
                                    </table>
                                </div>
                            </div>';
                }

                $response['content'] .= '
                        </div>
                    </div>';
            }
            break;

        case 'paiement':
            // Détails d'un remboursement (SANS agence_id)
            $sql = "SELECT r.*, u.nom, u.prenom, u.email, u.telephone, u.agence_id, a.nom as agence_nom
                    FROM remboursement r
                    JOIN users u ON r.utilisateur_id = u.id
                    LEFT JOIN agences a ON u.agence_id = a.id_agence
                    WHERE r.id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $paiement = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($paiement) {
                $response['title'] = "Remboursement #" . $id . " - " . htmlspecialchars($paiement['nom'] . ' ' . $paiement['prenom']);
                
                $badgeClass = $paiement['statut'] == 'remboursé' ? 'success' : ($paiement['statut'] == 'en attente' ? 'warning' : 'danger');

                $response['content'] = '
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Détails du remboursement</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr><th>ID Remboursement:</th><td>' . $paiement['id'] . '</td></tr>
                                        <tr><th>Client:</th><td>' . htmlspecialchars($paiement['nom'] . ' ' . $paiement['prenom']) . '</td></tr>
                                        <tr><th>Montant:</th><td class="text-success font-weight-bold">' . number_format($paiement['montant'], 0, ',', ' ') . ' Ar</td></tr>
                                        <tr><th>Date:</th><td>' . date('d/m/Y H:i', strtotime($paiement['date_remboursement'])) . '</td></tr>
                                        <tr><th>Statut:</th><td><span class="badge badge-' . $badgeClass . '">' . ucfirst($paiement['statut']) . '</span></td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-info card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Informations client</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr><th>ID Client:</th><td>' . $paiement['utilisateur_id'] . '</td></tr>
                                        <tr><th>Nom complet:</th><td>' . htmlspecialchars($paiement['nom'] . ' ' . $paiement['prenom']) . '</td></tr>
                                        <tr><th>Email:</th><td>' . htmlspecialchars($paiement['email']) . '</td></tr>
                                        <tr><th>Téléphone:</th><td>' . htmlspecialchars($paiement['telephone'] ?? '-') . '</td></tr>
                                        <tr><th>Agence:</th><td>' . htmlspecialchars($paiement['agence_nom'] ?? '-') . '</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>';
            }
            break;

        case 'client':
            // Détails d'un client
            $sql = "SELECT u.*, a.nom as agence_nom,
                           COUNT(DISTINCT dp.id) as nb_prets,
                           SUM(dp.montant) as montant_total,
                           COUNT(DISTINCT r.id) as nb_remboursements,
                           SUM(r.montant) as montant_rembourse
                    FROM users u
                    LEFT JOIN agences a ON u.agence_id = a.id_agence
                    LEFT JOIN demande_pret dp ON u.id = dp.utilisateur_id
                    LEFT JOIN remboursement r ON u.id = r.utilisateur_id
                    WHERE u.id = :id
                    GROUP BY u.id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($client) {
                $response['title'] = "Client #" . $id . " - " . htmlspecialchars($client['nom'] . ' ' . $client['prenom']);
                
                $montant_restant = $client['montant_total'] - $client['montant_rembourse'];

                $response['content'] = '
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Informations personnelles</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr><th>ID Client:</th><td>' . $client['id'] . '</td></tr>
                                        <tr><th>Nom complet:</th><td>' . htmlspecialchars($client['nom'] . ' ' . $client['prenom']) . '</td></tr>
                                        <tr><th>Email:</th><td>' . htmlspecialchars($client['email']) . '</td></tr>
                                        <tr><th>Téléphone:</th><td>' . htmlspecialchars($client['telephone'] ?? '-') . '</td></tr>
                                        <tr><th>Rôle:</th><td><span class="badge badge-info">' . ucfirst($client['role']) . '</span></td></tr>
                                        <tr><th>Agence:</th><td>' . htmlspecialchars($client['agence_nom'] ?? '-') . '</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-success card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Résumé financier</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr><th>Nombre de prêts:</th><td>' . ($client['nb_prets'] ?: 0) . '</td></tr>
                                        <tr><th>Montant total:</th><td class="text-primary">' . number_format($client['montant_total'] ?: 0, 0, ',', ' ') . ' Ar</td></tr>
                                        <tr><th>Montant remboursé:</th><td class="text-success">' . number_format($client['montant_rembourse'] ?: 0, 0, ',', ' ') . ' Ar</td></tr>
                                        <tr><th>Montant restant:</th><td class="text-danger">' . number_format($montant_restant ?: 0, 0, ',', ' ') . ' Ar</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>';
            }
            break;
    }

    if (empty($response['content'])) {
        http_response_code(404);
        echo json_encode(['error' => 'Élément non trouvé']);
    } else {
        echo json_encode($response);
    }

} catch(PDOException $e) {
    error_log("Erreur get_details.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur base de données: ' . $e->getMessage()]);
}
?>