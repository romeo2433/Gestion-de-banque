<?php
session_start();
require_once 'config.php';

// Vérification que l'utilisateur est bien un client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Récupérer les informations du client
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupérer les prêts du client
    $stmt_prets = $conn->prepare("
        SELECT * FROM demande_pret 
        WHERE utilisateur_id = :user_id 
        ORDER BY date_demande DESC
    ");
    $stmt_prets->execute([':user_id' => $user_id]);
    $prets = $stmt_prets->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les remboursements du client
    $stmt_remb = $conn->prepare("
        SELECT * FROM remboursement 
        WHERE utilisateur_id = :user_id 
        ORDER BY date_remboursement DESC
    ");
    $stmt_remb->execute([':user_id' => $user_id]);
    $remboursements = $stmt_remb->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    error_log("Erreur espace client: " . $e->getMessage());
    $error = "Une erreur technique est survenue.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGOR PRO | Espace Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), #224abe);
            padding: 1rem 2rem;
            box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .navbar-text {
            color: rgba(255,255,255,0.8) !important;
            margin-right: 1rem;
        }

        /* Cards */
        .card {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            color: var(--primary-color);
            font-weight: 600;
            padding: 1rem 1.5rem;
        }

        .card-header i {
            margin-right: 0.5rem;
        }

        /* Stats cards */
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), #224abe);
            color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .stat-card .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        /* Badges */
        .badge-success { background-color: var(--success-color); color: white; }
        .badge-warning { background-color: var(--warning-color); color: white; }
        .badge-danger { background-color: var(--danger-color); color: white; }
        .badge-info { background-color: var(--info-color); color: white; }

        /* Tables */
        .table {
            color: #5a5c69;
        }

        .table thead th {
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Welcome section */
        .welcome-section {
            background: white;
            border-radius: 0.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
        }

        .welcome-section h1 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .welcome-section p {
            color: var(--secondary-color);
            margin: 0;
        }

        /* Logout button */
        .btn-logout {
            background-color: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background-color: rgba(255,255,255,0.3);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-university me-2"></i>IGOR PRO
            </a>
            <div class="d-flex align-items-center">
                <span class="navbar-text">
                    <i class="fas fa-user-circle me-2"></i>
                    <?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?>
                </span>
                <a href="logout.php" class="btn-logout" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">
                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Message d'erreur -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Section de bienvenue -->
        <div class="welcome-section">
            <h1>Bonjour, <?php echo htmlspecialchars($client['prenom']); ?> !</h1>
            <p><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($client['email']); ?></p>
        </div>

        <!-- Statistiques -->
        <div class="row mb-4">
        <div class="col-md-4">
    <div class="stat-card">

        <p class="stat-value">
            <?php echo count($prets); ?>
        </p>

        <p class="stat-label">
            Demandes de prêt
        </p>

        <a href="client/nouvelle_demande.php"
           class="btn btn-light mt-3">

            <i class="fas fa-plus"></i>
            Nouvelle demande

        </a>

    </div>
</div>
            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, var(--success-color), #17a673);">
                    <p class="stat-value"><?php echo count($remboursements); ?></p>
                    <p class="stat-label">Remboursements effectués</p>
                </div>
            </div>
            <div class="col-md-4">
                <?php 
                $montant_total = 0;
                foreach ($prets as $pret) {
                    if ($pret['statut'] == 'approuvé') {
                        $montant_total += $pret['montant'];
                    }
                }
                ?>
                <div class="stat-card" style="background: linear-gradient(135deg, var(--info-color), #2c9faf);">
                    <p class="stat-value"><?php echo number_format($montant_total, 0, ',', ' '); ?> Ar</p>
                    <p class="stat-label">Montant total des prêts</p>
                </div>
            </div>
        </div>

        <!-- Mes prêts -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-hand-holding-usd"></i>
                Mes demandes de prêt
            </div>
            <div class="card-body">
                <?php if (empty($prets)): ?>
                    <p class="text-center text-muted my-5">
                        <i class="fas fa-inbox fa-3x mb-3"></i><br>
                        Vous n'avez pas encore de demande de prêt.
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Montant</th>
                                    <th>Durée</th>
                                    <th>Taux</th>
                                    <th>Statut</th>
                                    <th>Date demande</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prets as $pret): ?>
                                <tr>
                                    <td>#<?php echo $pret['id']; ?></td>
                                    <td><?php echo number_format($pret['montant'], 0, ',', ' '); ?> Ar</td>
                                    <td><?php echo $pret['duree']; ?> mois</td>
                                    <td><?php echo $pret['taux_interet']; ?>%</td>
                                    <td>
                                        <?php
                                        $badgeClass = 'secondary';
                                        switch($pret['statut']) {
                                            case 'approuvé': $badgeClass = 'success'; break;
                                            case 'en attente': $badgeClass = 'warning'; break;
                                            case 'refusé': $badgeClass = 'danger'; break;
                                        }
                                        ?>
                                        <span class="badge badge-<?php echo $badgeClass; ?>">
                                            <?php echo ucfirst($pret['statut']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($pret['date_demande'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Historique des remboursements -->
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-history"></i>
                Historique des remboursements
            </div>
            <div class="card-body">
                <?php if (empty($remboursements)): ?>
                    <p class="text-center text-muted my-5">
                        <i class="fas fa-coins fa-3x mb-3"></i><br>
                        Aucun remboursement enregistré.
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Montant</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($remboursements as $remb): ?>
                                <tr>
                                    <td>#<?php echo $remb['id']; ?></td>
                                    <td><?php echo number_format($remb['montant'], 0, ',', ' '); ?> Ar</td>
                                    <td><?php echo date('d/m/Y', strtotime($remb['date_remboursement'])); ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = 'secondary';
                                        switch($remb['statut']) {
                                            case 'remboursé': $badgeClass = 'success'; break;
                                            case 'en attente': $badgeClass = 'warning'; break;
                                            case 'annulé': $badgeClass = 'danger'; break;
                                        }
                                        ?>
                                        <span class="badge badge-<?php echo $badgeClass; ?>">
                                            <?php echo ucfirst($remb['statut']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>