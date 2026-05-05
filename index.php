<?php
session_start();

// Si déjà connecté, rediriger vers le bon espace
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'super_admin' || $_SESSION['role'] == 'admin_regional' || 
        $_SESSION['role'] == 'chef_agence' || $_SESSION['role'] == 'agent') {
        header("Location: dashboard.php");
    } else {
        header("Location: espace_client.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGOR PRO - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-admin: #1e3a8a;
            --primary-client: #0d9488;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }
        .container {
            max-width: 1200px;
        }
        h1 {
            color: white;
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 800;
            text-shadow: 2px 4px 8px rgba(0,0,0,0.3);
            font-size: 3rem;
        }
        h1 i {
            margin-right: 1rem;
            color: #ffd700;
        }
        .card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            cursor: pointer;
        }
        .card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0,0,0,0.4);
        }
        .card-header {
            padding: 2rem;
            text-align: center;
            border-bottom: none;
        }
        .card-header i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .card-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .card-body {
            padding: 2rem;
            text-align: center;
            background: white;
        }
        .badge-role {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .feature-list {
            text-align: left;
            margin: 1.5rem 0;
            padding-left: 1rem;
        }
        .feature-list li {
            margin-bottom: 0.8rem;
            list-style: none;
        }
        .feature-list i {
            margin-right: 0.8rem;
            width: 20px;
        }
        .btn-role {
            padding: 0.8rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            width: 100%;
            border: none;
            color: white;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-role:hover {
            transform: scale(1.05);
            color: white;
        }
        .admin-card .card-header {
            background: linear-gradient(135deg, var(--primary-admin), #0f172a);
            color: white;
        }
        .client-card .card-header {
            background: linear-gradient(135deg, var(--primary-client), #115e59);
            color: white;
        }
        .btn-admin {
            background: linear-gradient(135deg, var(--primary-admin), #0f172a);
        }
        .btn-client {
            background: linear-gradient(135deg, var(--primary-client), #115e59);
        }
        .badge-pro {
            background: #ffd700;
            color: #1e3a8a;
        }
        .footer {
            text-align: center;
            margin-top: 3rem;
            color: white;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-university"></i>
            IGOR PRO
        </h1>
        
        <div class="row g-4">
            <!-- Carte Super Admin -->
            <div class="col-md-6">
                <div class="card admin-card" onclick="window.location.href='login.php?role=super_admin'">
                    <div class="card-header">
                        <i class="fas fa-crown"></i>
                        <h2>Super Admin</h2>
                        <div class="badge-role badge-pro">👑 Contrôle Total</div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Accès complet à toutes les fonctionnalités :</p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle text-success"></i> Dashboard analytique en temps réel</li>
                            <li><i class="fas fa-check-circle text-success"></i> Gestion de toutes les agences</li>
                            <li><i class="fas fa-check-circle text-success"></i> Création et gestion des agents</li>
                            <li><i class="fas fa-check-circle text-success"></i> Suivi global des prêts</li>
                            <li><i class="fas fa-check-circle text-success"></i> Rapports financiers exportables</li>
                            <li><i class="fas fa-check-circle text-success"></i> Audit et sécurité</li>
                        </ul>
                        <a href="login.php?role=super_admin" class="btn-role btn-admin">
                            <i class="fas fa-sign-in-alt me-2"></i>Connexion Admin
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Carte Client -->
            <div class="col-md-6">
                <div class="card client-card" onclick="window.location.href='login.php?role=client'">
                    <div class="card-header">
                        <i class="fas fa-user-circle"></i>
                        <h2>Client</h2>
                        <div class="badge-role badge-light">👤 Espace Personnel</div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Gérez vos prêts en toute simplicité :</p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle text-success"></i> Espace personnel sécurisé</li>
                            <li><i class="fas fa-check-circle text-success"></i> Simulateur de prêt interactif</li>
                            <li><i class="fas fa-check-circle text-success"></i> Suivi des remboursements</li>
                            <li><i class="fas fa-check-circle text-success"></i> Calendrier des échéances</li>
                            <li><i class="fas fa-check-circle text-success"></i> Téléchargement des échéanciers PDF</li>
                            <li><i class="fas fa-check-circle text-success"></i> Notifications automatiques</li>
                        </ul>
                        <a href="login.php?role=client" class="btn-role btn-client">
                            <i class="fas fa-sign-in-alt me-2"></i>Espace Client
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section "Autres rôles" (optionnel) -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="text-center text-white">
                    <p class="mb-2">Également disponible :</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <span class="badge bg-secondary p-2">👥 Admin Régional</span>
                        <span class="badge bg-secondary p-2">🏦 Chef d'Agence</span>
                        <span class="badge bg-secondary p-2">💼 Agent Bancaire</span>
                    </div>
                    <p class="mt-3 small opacity-75">
                        <i class="fas fa-info-circle me-1"></i>
                        Ces rôles sont accessibles via connexion directe
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <i class="fas fa-shield-alt me-2"></i>
                &copy; 2023-<?php echo date('Y'); ?> IGOR PRO. Sécurité bancaire niveau entreprise.
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>