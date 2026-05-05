<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGOR PRO - Accès Refusé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.2);
        }
        .card-header {
            background: linear-gradient(135deg, #e74a3b, #c0392b);
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 15px 15px 0 0 !important;
        }
        .card-header i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .card-body {
            padding: 3rem;
            text-align: center;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4e73df, #224abe);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        }
        .btn-success {
            background: linear-gradient(135deg, #1cc88a, #17a673);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-shield-alt"></i>
                        <h2 class="mt-3">Accès Refusé</h2>
                    </div>
                    <div class="card-body">
                        <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                        
                        <?php if (isset($_GET['erreur'])): ?>
                            <?php if ($_GET['erreur'] == 'agence'): ?>
                                <h4 class="mb-3">Agence non assignée</h4>
                                <p class="text-muted mb-4">
                                    Vous n'êtes pas rattaché à une agence.<br>
                                    Veuillez contacter l'administrateur.
                                </p>
                            <?php elseif ($_GET['erreur'] == 'technique'): ?>
                                <h4 class="mb-3">Erreur technique</h4>
                                <p class="text-muted mb-4">
                                    Une erreur technique est survenue.<br>
                                    Veuillez réessayer plus tard.
                                </p>
                            <?php else: ?>
                                <h4 class="mb-3">Droits insuffisants</h4>
                                <p class="text-muted mb-4">
                                    Votre rôle ne vous permet pas d'accéder à cette page.
                                </p>
                            <?php endif; ?>
                        <?php else: ?>
                            <h4 class="mb-3">Droits insuffisants</h4>
                            <p class="text-muted mb-4">
                                Vous n'avez pas les permissions nécessaires.<br>
                                Contactez l'administrateur si vous pensez que c'est une erreur.
                            </p>
                        <?php endif; ?>

                        <div class="mt-4">
                            <a href="dashboard.php" class="btn btn-primary me-2">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </a>
                            <a href="logout.php" class="btn btn-success">
                                <i class="fas fa-sign-out-alt me-2"></i>Se reconnecter
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>