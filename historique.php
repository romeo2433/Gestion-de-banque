<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';
require_once 'verification_role.php';

// Vérifier que l'utilisateur a le droit d'accéder à cette page
est_connecte();
verifier_acces(['super_admin', 'admin_regional', 'chef_agence', 'agent'], true);

// Récupération des paramètres de filtre
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$dateDebut = isset($_GET['dateDebut']) ? $_GET['dateDebut'] : '';
$dateFin = isset($_GET['dateFin']) ? $_GET['dateFin'] : '';

$condition_agence = condition_agence('u');

try {
    // 1. Récupération des prêts (demande_pret) avec infos utilisateurs et agence
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

    // 2. Récupération des paiements/remboursements
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

    // 3. Récupération des clients avec prêts actifs
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
        'approuves' => count(array_filter($prets, function($p) { return $p['statut'] == 'approuvé'; })),
        'en_attente' => count(array_filter($prets, function($p) { return $p['statut'] == 'en attente'; })),
        'refuses' => count(array_filter($prets, function($p) { return $p['statut'] == 'refusé'; }))
    ];

    $statsPaiements = [
        'total' => count($paiements),
        'rembourses' => count(array_filter($paiements, function($p) { return $p['statut'] == 'remboursé'; })),
        'en_attente' => count(array_filter($paiements, function($p) { return $p['statut'] == 'en attente'; })),
        'annules' => count(array_filter($paiements, function($p) { return $p['statut'] == 'annulé'; }))
    ];

    $statsClients = [
        'total' => count($clients),
        'actifs' => count(array_filter($clients, function($c) { return $c['statut_global'] == 'Actif'; })),
        'retard' => count(array_filter($clients, function($c) { return $c['statut_global'] == 'En retard'; }))
    ];

    // Journaliser la consultation
    journal_action('consultation_historique', "Filtres - Statut: $statusFilter, Dates: $dateDebut - $dateFin");

} catch(PDOException $e) {
    error_log("Erreur historique.php: " . $e->getMessage());
    $error = "Erreur : " . $e->getMessage();
    $prets = [];
    $paiements = [];
    $clients = [];
    $statsPrets = ['total' => 0, 'approuves' => 0, 'en_attente' => 0, 'refuses' => 0];
    $statsPaiements = ['total' => 0, 'rembourses' => 0, 'en_attente' => 0, 'annules' => 0];
    $statsClients = ['total' => 0, 'actifs' => 0, 'retard' => 0];
}

$username = $_SESSION['username'] ?? 'Utilisateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>IGOR PRO | Historique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Source Sans Pro', sans-serif;
        }

        /* Header et navigation */
        .main-header {
            background: linear-gradient(135deg, var(--primary-color), #224abe);
            border-bottom: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .main-header .navbar-nav .nav-link {
            color: white !important;
        }

        .main-sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .brand-link {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1.5rem 1rem;
        }

        .brand-text {
            font-weight: 700;
            letter-spacing: 0.05em;
            color: white !important;
        }

        .user-panel {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem;
        }

        .user-panel .info a {
            color: white !important;
            font-weight: 600;
        }

        .nav-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            margin: 0.2rem 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }

        .nav-sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 600;
            border-left: 4px solid #ffd700;
        }

        .nav-sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        /* Info rôle */
        .info-role {
            background-color: #e3e6f0;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-color);
        }

        /* Content Wrapper */
        .content-wrapper {
            background-color: #f8f9fc;
        }

        /* Cards */
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: none;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }

        .card-header .card-title {
            font-size: 1.1rem;
            margin: 0;
        }

        .card-tools .badge {
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 0.25rem;
        }

        /* Badges personnalisés */
        .badge-primary { background-color: var(--primary-color); color: white; }
        .badge-success { background-color: var(--success-color); color: white; }
        .badge-warning { background-color: var(--warning-color); color: white; }
        .badge-danger { background-color: var(--danger-color); color: white; }
        .badge-info { background-color: var(--info-color); color: white; }
        .badge-secondary { background-color: var(--secondary-color); color: white; }

        /* Tables */
        .table {
            color: #5a5c69;
        }

        .table thead th {
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            color: var(--primary-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
        }

        .table td, .table th {
            vertical-align: middle;
            padding: 0.75rem;
        }

        .text-right-small {
            text-align: right;
            font-weight: 500;
            color: var(--dark-color);
        }

        /* Action buttons */
        .action-buttons .btn {
            margin: 0 2px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 0.25rem;
            transition: all 0.2s;
        }

        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .btn-info { background-color: var(--info-color); border-color: var(--info-color); color: white; }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); color: white; }
        .btn-success { background-color: var(--success-color); border-color: var(--success-color); color: white; }
        .btn-warning { background-color: var(--warning-color); border-color: var(--warning-color); color: white; }
        .btn-danger { background-color: var(--danger-color); border-color: var(--danger-color); color: white; }

        /* Filtres */
        .filters-card {
            background: linear-gradient(to right, white, #f8f9fc);
        }

        .form-control, .form-select {
            border: 1px solid #d1d3e2;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .input-group-text {
            background-color: #f8f9fc;
            border: 1px solid #d1d3e2;
            border-right: none;
            color: var(--primary-color);
        }

        .input-group .form-control {
            border-left: none;
        }

        /* Pagination */
        .dataTables_paginate .paginate_button {
            border-radius: 0.25rem !important;
            margin: 0 2px;
        }

        .dataTables_paginate .paginate_button.current {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
        }

        /* Modal */
        .modal-content {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), #224abe);
            color: white;
            border-radius: 0.5rem 0.5rem 0 0;
            padding: 1.5rem;
        }

        .modal-header .close {
            color: white;
            opacity: 0.8;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        .modal-title {
            font-weight: 600;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            border-top: 1px solid #e3e6f0;
            padding: 1.5rem;
        }

        /* Footer */
        .main-footer {
            background: white;
            border-top: 1px solid #e3e6f0;
            padding: 1rem;
            color: var(--secondary-color);
        }

        .main-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .main-footer a:hover {
            text-decoration: underline;
        }

        /* Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Stats cards */
        .stats-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 500;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .stats-badge i {
            margin-right: 0.5rem;
            font-size: 1rem;
        }

        .stats-badge.bg-primary { background: linear-gradient(135deg, var(--primary-color), #224abe) !important; }
        .stats-badge.bg-success { background: linear-gradient(135deg, var(--success-color), #17a673) !important; }
        .stats-badge.bg-warning { background: linear-gradient(135deg, var(--warning-color), #f4b619) !important; }
        .stats-badge.bg-danger { background: linear-gradient(135deg, var(--danger-color), #d52a1a) !important; }
        .stats-badge.bg-info { background: linear-gradient(135deg, var(--info-color), #2c9faf) !important; }

        /* Responsive */
        @media (max-width: 768px) {
            .card-tools {
                margin-top: 1rem;
                text-align: left;
            }
            
            .stats-badge {
                margin-bottom: 0.5rem;
            }
            
            .table-responsive {
                border-radius: 0.5rem;
            }
        }

        /* Print styles */
        .print-content {
            display: none;
        }
        
        @media print {
            body * { visibility: hidden; }
            .print-content, .print-content * { visibility: visible; }
            .print-content { 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 100%; 
                display: block !important; 
                background: white; 
                padding: 20px; 
            }
            .print-content h2, .print-content h3 { 
                color: #000 !important; 
                margin-bottom: 15px; 
            }
            .print-content p { 
                margin-bottom: 8px; 
                font-size: 14px; 
            }
            .print-content strong { 
                font-weight: bold; 
            }
            .print-date { 
                margin-top: 30px; 
                text-align: center; 
                font-size: 12px; 
                color: #666; 
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="dashboard.php" class="brand-link">
                <img src="https://via.placeholder.com/50" alt="Logo" class="brand-image img-circle elevation-3">
                <span class="brand-text font-weight-light">IGOR PRO</span>
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="info">
                        <a href="#" class="d-block">
                            <i class="fas fa-user-circle mr-2"></i>
                            <?php echo htmlspecialchars($username); ?>
                        </a>
                    </div>
                </div>
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="utilisateur.php" class="nav-link">
                                <i class="fas fa-users"></i>
                                <p>Clients</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="demande.php" class="nav-link">
                                <i class="fas fa-hand-holding-usd"></i>
                                <p>Demandes de prêt</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="planification.php" class="nav-link">
                                <i class="fas fa-calendar-alt"></i>
                                <p>Échéanciers</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="remboursement.php" class="nav-link">
                                <i class="fas fa-money-bill-wave"></i>
                                <p>Remboursement</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="historique.php" class="nav-link active">
                                <i class="fas fa-history"></i>
                                <p>Historique</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Agents Bancaires.php" class="nav-link">
                                <i class="fas fa-user-shield"></i>
                                <p>Agents Bancaires</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-primary">
                                <i class="fas fa-history mr-2"></i>
                                Historique des opérations
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Accueil</a></li>
                                <li class="breadcrumb-item active">Historique</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <?php echo $error; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Info rôle -->
                    <div class="info-role">
                        <i class="fas fa-user-shield mr-2 text-primary"></i>
                        Connecté en tant que : <strong><?php echo $_SESSION['role']; ?></strong>
                        <?php if (isset($_SESSION['agence_id'])): ?>
                            | <i class="fas fa-building mr-1"></i>Agence: <strong><?php 
                                try {
                                    $stmt = $conn->prepare("SELECT nom FROM agences WHERE id_agence = ?");
                                    $stmt->execute([$_SESSION['agence_id']]);
                                    $agence = $stmt->fetch();
                                    echo $agence ? htmlspecialchars($agence['nom']) : 'ID ' . $_SESSION['agence_id'];
                                } catch(Exception $e) {
                                    echo 'ID ' . $_SESSION['agence_id'];
                                }
                            ?></strong>
                        <?php endif; ?>
                    </div>

                    <!-- Filtres -->
                    <div class="row fade-in">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-filter mr-2"></i>
                                    Filtres de recherche
                                </div>
                                <div class="card-body">
                                    <form action="" method="GET" class="form-row align-items-end">
                                        <div class="form-group col-md-3">
                                            <label for="status" class="form-label">Statut</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="">Tous les statuts</option>
                                                <option value="approuvé" <?php echo $statusFilter == 'approuvé' ? 'selected' : ''; ?>>Approuvé</option>
                                                <option value="en attente" <?php echo $statusFilter == 'en attente' ? 'selected' : ''; ?>>En attente</option>
                                                <option value="refusé" <?php echo $statusFilter == 'refusé' ? 'selected' : ''; ?>>Refusé</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="dateDebut" class="form-label">Date début</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                </div>
                                                <input type="date" class="form-control" id="dateDebut" name="dateDebut" value="<?php echo htmlspecialchars($dateDebut); ?>">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="dateFin" class="form-label">Date fin</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                </div>
                                                <input type="date" class="form-control" id="dateFin" name="dateFin" value="<?php echo htmlspecialchars($dateFin); ?>">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-search mr-2"></i>Filtrer
                                            </button>
                                            <a href="historique.php" class="btn btn-secondary btn-block mt-2">
                                                <i class="fas fa-undo mr-2"></i>Réinitialiser
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 1: Demandes de prêt -->
                    <div class="row fade-in" style="animation-delay: 0.1s">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <i class="fas fa-hand-holding-usd mr-2 text-primary"></i>
                                            Historique des demandes de prêt
                                        </div>
                                        <div class="col-md-6 text-md-right">
                                            <span class="stats-badge bg-primary">
                                                <i class="fas fa-chart-bar"></i>
                                                Total: <?php echo $statsPrets['total']; ?>
                                            </span>
                                            <span class="stats-badge bg-success">
                                                <i class="fas fa-check-circle"></i>
                                                <?php echo $statsPrets['approuves']; ?> approuvés
                                            </span>
                                            <span class="stats-badge bg-warning">
                                                <i class="fas fa-clock"></i>
                                                <?php echo $statsPrets['en_attente']; ?> en attente
                                            </span>
                                            <span class="stats-badge bg-danger">
                                                <i class="fas fa-times-circle"></i>
                                                <?php echo $statsPrets['refuses']; ?> refusés
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="pretTable" class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Client</th>
                                                    <th>Montant</th>
                                                    <th>Durée</th>
                                                    <th>Taux</th>
                                                    <th>Statut</th>
                                                    <th>Date demande</th>
                                                    <th>Agence</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($prets)): ?>
                                                <tr>
                                                    <td colspan="9" class="text-center py-5">
                                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">Aucune demande trouvée</p>
                                                    </td>
                                                </tr>
                                                <?php else: ?>
                                                    <?php foreach ($prets as $pret): ?>
                                                    <tr>
                                                        <td><span class="badge badge-secondary">#<?php echo $pret['id']; ?></span></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($pret['nom'] . ' ' . $pret['prenom']); ?></strong>
                                                        </td>
                                                        <td class="text-right-small"><?php echo number_format($pret['montant'], 0, ',', ' '); ?> Ar</td>
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
                                                        <td>
                                                            <?php if (!empty($pret['agence_nom'])): ?>
                                                                <span class="badge badge-info">
                                                                    <i class="fas fa-building mr-1"></i>
                                                                    <?php echo htmlspecialchars($pret['agence_nom']); ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="action-buttons text-center">
                                                            <button onclick="showDetails('pret', <?php echo $pret['id']; ?>)" 
                                                                    class="btn btn-info btn-sm" 
                                                                    title="Voir détails"
                                                                    data-toggle="tooltip">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button onclick="printDetails('pret', <?php echo $pret['id']; ?>)" 
                                                                    class="btn btn-primary btn-sm" 
                                                                    title="Imprimer"
                                                                    data-toggle="tooltip">
                                                                <i class="fas fa-print"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Remboursements -->
                    <div class="row fade-in" style="animation-delay: 0.2s">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <i class="fas fa-money-bill-wave mr-2 text-success"></i>
                                            Historique des remboursements
                                        </div>
                                        <div class="col-md-6 text-md-right">
                                            <span class="stats-badge bg-primary">
                                                <i class="fas fa-chart-bar"></i>
                                                Total: <?php echo $statsPaiements['total']; ?>
                                            </span>
                                            <span class="stats-badge bg-success">
                                                <i class="fas fa-check-circle"></i>
                                                <?php echo $statsPaiements['rembourses']; ?> remboursés
                                            </span>
                                            <span class="stats-badge bg-warning">
                                                <i class="fas fa-clock"></i>
                                                <?php echo $statsPaiements['en_attente']; ?> en attente
                                            </span>
                                            <span class="stats-badge bg-danger">
                                                <i class="fas fa-times-circle"></i>
                                                <?php echo $statsPaiements['annules']; ?> annulés
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="paiementsTable" class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Client</th>
                                                    <th>Montant</th>
                                                    <th>Date</th>
                                                    <th>Statut</th>
                                                    <th>Agence</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($paiements)): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center py-5">
                                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">Aucun remboursement trouvé</p>
                                                    </td>
                                                </tr>
                                                <?php else: ?>
                                                    <?php foreach ($paiements as $paiement): ?>
                                                    <tr>
                                                        <td><span class="badge badge-secondary">#<?php echo $paiement['id']; ?></span></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($paiement['nom'] . ' ' . $paiement['prenom']); ?></strong>
                                                        </td>
                                                        <td class="text-right-small"><?php echo number_format($paiement['montant'], 0, ',', ' '); ?> Ar</td>
                                                        <td><?php echo date('d/m/Y', strtotime($paiement['date_remboursement'])); ?></td>
                                                        <td>
                                                            <?php
                                                            $badgeClass = 'secondary';
                                                            switch($paiement['statut']) {
                                                                case 'remboursé': $badgeClass = 'success'; break;
                                                                case 'en attente': $badgeClass = 'warning'; break;
                                                                case 'annulé': $badgeClass = 'danger'; break;
                                                            }
                                                            ?>
                                                            <span class="badge badge-<?php echo $badgeClass; ?>">
                                                                <?php echo ucfirst($paiement['statut']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($paiement['agence_nom'])): ?>
                                                                <span class="badge badge-info">
                                                                    <i class="fas fa-building mr-1"></i>
                                                                    <?php echo htmlspecialchars($paiement['agence_nom']); ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="action-buttons text-center">
                                                            <button onclick="showDetails('paiement', <?php echo $paiement['id']; ?>)" 
                                                                    class="btn btn-info btn-sm" 
                                                                    title="Voir détails"
                                                                    data-toggle="tooltip">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button onclick="printDetails('paiement', <?php echo $paiement['id']; ?>)" 
                                                                    class="btn btn-primary btn-sm" 
                                                                    title="Imprimer"
                                                                    data-toggle="tooltip">
                                                                <i class="fas fa-print"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Clients -->
                    <div class="row fade-in" style="animation-delay: 0.3s">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <i class="fas fa-users mr-2 text-info"></i>
                                            Liste des clients
                                        </div>
                                        <div class="col-md-6 text-md-right">
                                            <span class="stats-badge bg-primary">
                                                <i class="fas fa-users"></i>
                                                <?php echo $statsClients['total']; ?> clients
                                            </span>
                                            <span class="stats-badge bg-success">
                                                <i class="fas fa-check-circle"></i>
                                                <?php echo $statsClients['actifs']; ?> actifs
                                            </span>
                                            <span class="stats-badge bg-danger">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <?php echo $statsClients['retard']; ?> en retard
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="clientsTable" class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nom complet</th>
                                                    <th>Email</th>
                                                    <th>Téléphone</th>
                                                    <th>Prêts</th>
                                                    <th>Montant total</th>
                                                    <th>Remboursé</th>
                                                    <th>Restant</th>
                                                    <th>Statut</th>
                                                    <th>Agence</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($clients)): ?>
                                                <tr>
                                                    <td colspan="11" class="text-center py-5">
                                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">Aucun client trouvé</p>
                                                    </td>
                                                </tr>
                                                <?php else: ?>
                                                    <?php foreach ($clients as $client): ?>
                                                    <?php 
                                                        $reste = $client['montant_total_prets'] - $client['total_rembourse'];
                                                    ?>
                                                    <tr>
                                                        <td><span class="badge badge-secondary">#<?php echo $client['id']; ?></span></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($client['nom'] . ' ' . $client['prenom']); ?></strong>
                                                        </td>
                                                        <td>
                                                            <a href="mailto:<?php echo htmlspecialchars($client['email']); ?>" class="text-decoration-none">
                                                                <i class="fas fa-envelope mr-1 text-primary"></i>
                                                                <?php echo htmlspecialchars($client['email']); ?>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($client['telephone'])): ?>
                                                            <a href="tel:<?php echo htmlspecialchars($client['telephone']); ?>" class="text-decoration-none">
                                                                <i class="fas fa-phone mr-1 text-success"></i>
                                                                <?php echo htmlspecialchars($client['telephone']); ?>
                                                            </a>
                                                            <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $client['nb_prets'] ?: 0; ?></td>
                                                        <td class="text-right-small"><?php echo number_format($client['montant_total_prets'] ?: 0, 0, ',', ' '); ?> Ar</td>
                                                        <td class="text-right-small text-success"><?php echo number_format($client['total_rembourse'] ?: 0, 0, ',', ' '); ?> Ar</td>
                                                        <td class="text-right-small text-danger"><?php echo number_format($reste ?: 0, 0, ',', ' '); ?> Ar</td>
                                                        <td>
                                                            <?php
                                                            $badgeClass = 'secondary';
                                                            switch($client['statut_global']) {
                                                                case 'Actif': $badgeClass = 'success'; break;
                                                                case 'En retard': $badgeClass = 'danger'; break;
                                                                case 'Inactif': $badgeClass = 'secondary'; break;
                                                            }
                                                            ?>
                                                            <span class="badge badge-<?php echo $badgeClass; ?>">
                                                                <?php echo $client['statut_global']; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($client['agence_nom'])): ?>
                                                                <span class="badge badge-info">
                                                                    <i class="fas fa-building mr-1"></i>
                                                                    <?php echo htmlspecialchars($client['agence_nom']); ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="action-buttons text-center">
                                                            <button onclick="showDetails('client', <?php echo $client['id']; ?>)" 
                                                                    class="btn btn-info btn-sm" 
                                                                    title="Voir détails"
                                                                    data-toggle="tooltip">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button onclick="printDetails('client', <?php echo $client['id']; ?>)" 
                                                                    class="btn btn-primary btn-sm" 
                                                                    title="Imprimer"
                                                                    data-toggle="tooltip">
                                                                <i class="fas fa-print"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 2.0.0
            </div>
            <strong>&copy; 2023-<?php echo date('Y'); ?> <a href="#">IGOR PRO</a>.</strong> Tous droits réservés.
        </footer>
    </div>

    <!-- Print Section -->
    <div class="print-content" id="printContent">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2>IGOR PRO - Rapport détaillé</h2>
        </div>
        <div id="printDetailsContent"></div>
        <div class="print-date">
            <p>Imprimé le: <span id="printDate"></span> par <?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">
                        <i class="fas fa-info-circle mr-2"></i>
                        Détails
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalBodyContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
                        <p class="mt-2 text-muted">Chargement des détails...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Fermer
                    </button>
                    <button type="button" class="btn btn-primary" onclick="printModalContent()">
                        <i class="fas fa-print mr-2"></i>Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

    <script>
        $(function () {
            // Initialisation des DataTables
            <?php if (!empty($prets)): ?>
            $("#pretTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                "language": { 
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json" 
                },
                "order": [[6, "desc"]],
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Tous"]]
            });
            <?php endif; ?>
            
            <?php if (!empty($paiements)): ?>
            $("#paiementsTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                "language": { 
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json" 
                },
                "order": [[3, "desc"]],
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Tous"]]
            });
            <?php endif; ?>
            
            <?php if (!empty($clients)): ?>
            $("#clientsTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                "language": { 
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json" 
                },
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Tous"]]
            });
            <?php endif; ?>

            // Initialisation des tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });

        // Fonction pour afficher les détails
        function showDetails(type, id) {
            $('#modalBodyContent').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                    <p class="mt-2 text-muted">Chargement des détails...</p>
                </div>
            `);
            
            $('#detailsModalLabel').html(`<i class="fas fa-info-circle mr-2"></i>Chargement...`);
            $('#detailsModal').modal('show');

            $.ajax({
                url: 'get_details.php',
                type: 'GET',
                data: { type: type, id: id },
                dataType: 'json',
                success: function(response) {
                    $('#detailsModalLabel').html(`<i class="fas fa-info-circle mr-2"></i>${response.title || 'Détails'}`);
                    $('#modalBodyContent').html(response.content || '<p class="text-center text-muted my-5">Aucune information disponible</p>');
                },
                error: function(xhr, status, error) {
                    let errorMsg = 'Erreur lors du chargement des détails';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) errorMsg = response.error;
                    } catch(e) {}
                    
                    $('#modalBodyContent').html(`
                        <div class="alert alert-danger m-3">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            ${errorMsg}
                        </div>
                    `);
                }
            });
        }

        // Fonction pour imprimer les détails
        function printDetails(type, id) {
            const printContent = document.getElementById('printDetailsContent');
            printContent.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                    <p class="mt-2">Préparation de l'impression...</p>
                </div>
            `;

            $.ajax({
                url: 'get_details.php',
                type: 'GET',
                data: { type: type, id: id },
                dataType: 'json',
                success: function(response) {
                    const now = new Date();
                    const dateStr = now.toLocaleDateString('fr-FR') + ' à ' + now.toLocaleTimeString('fr-FR');
                    document.getElementById('printDate').textContent = dateStr;
                    
                    let content = response.content || '<p>Aucune information disponible</p>';
                    content = content.replace(/<canvas[\s\S]*?<\/canvas>/g, '(Graphique)');
                    content = content.replace(/<script[\s\S]*?<\/script>/g, '');
                    
                    printContent.innerHTML = `
                        <h3 class="text-center mb-4" style="color: #4e73df;">${response.title || 'Détails'}</h3>
                        <hr style="border-top: 2px solid #4e73df;">
                        ${content}
                    `;
                    
                    setTimeout(() => window.print(), 500);
                },
                error: function(xhr, status, error) {
                    let errorMsg = 'Erreur lors du chargement des détails';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) errorMsg = response.error;
                    } catch(e) {}
                    
                    printContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            ${errorMsg}
                        </div>
                    `;
                    
                    setTimeout(() => window.print(), 500);
                }
            });
        }

        // Fonction pour imprimer le contenu de la modal
        function printModalContent() {
            const content = $('#modalBodyContent').html();
            const title = $('#detailsModalLabel').text();
            
            let cleanContent = content.replace(/<canvas[\s\S]*?<\/canvas>/g, '(Graphique)');
            cleanContent = cleanContent.replace(/<script[\s\S]*?<\/script>/g, '');
            
            const now = new Date();
            const dateStr = now.toLocaleDateString('fr-FR') + ' à ' + now.toLocaleTimeString('fr-FR');
            document.getElementById('printDate').textContent = dateStr;
            
            document.getElementById('printDetailsContent').innerHTML = `
                <h3 class="text-center mb-4" style="color: #4e73df;">${title}</h3>
                <hr style="border-top: 2px solid #4e73df;">
                ${cleanContent}
            `;
            
            $('#detailsModal').modal('hide');
            
            setTimeout(() => window.print(), 500);
        }
    </script>
</body>
</html>