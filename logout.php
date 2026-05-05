<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion - IGOR Banking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap');

        :root {
            --dark-green: #004d40;
            --medium-green: #00796b;
            --light-green: #e0f2f1;
            --accent-gold: #cdad70;
            --success-green: #28a745;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, var(--light-green), #f5f7fa);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .logout-container {
            max-width: 600px;
            width: 100%;
        }

        .logout-card {
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 77, 64, 0.2);
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logout-header {
            background: linear-gradient(135deg, var(--success-green), #20c997);
            padding: 50px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .logout-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .success-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 25px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            animation: scaleIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0) rotate(-180deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
            }
        }

        .success-icon i {
            font-size: 4.5rem;
            color: white;
        }

        .checkmark-circle {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: checkmarkGrow 0.8s ease-out;
        }

        @keyframes checkmarkGrow {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                transform: scale(1);
            }
        }

        .logout-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: white;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }

        .logout-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.95);
            position: relative;
            z-index: 2;
        }

        .logout-body {
            padding: 45px 40px;
            text-align: center;
        }

        .logout-message {
            font-size: 1.1rem;
            color: #495057;
            margin-bottom: 30px;
            line-height: 1.7;
        }

        .security-info {
            background: linear-gradient(135deg, var(--light-green), #f0f8f7);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 35px;
            border-left: 4px solid var(--success-green);
        }

        .security-info-title {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 700;
            color: var(--dark-green);
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .security-info-title i {
            color: var(--success-green);
            font-size: 1.3rem;
        }

        .security-items {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .security-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #495057;
            font-size: 0.95rem;
        }

        .security-item i {
            color: var(--success-green);
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-reconnect {
            flex: 1;
            min-width: 200px;
            padding: 18px;
            background: linear-gradient(to right, var(--dark-green), var(--medium-green));
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.4s;
            box-shadow: 0 8px 25px rgba(0, 77, 64, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-reconnect::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-reconnect:hover::before {
            left: 100%;
        }

        .btn-reconnect:hover {
            background: linear-gradient(to right, var(--medium-green), var(--dark-green));
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 77, 64, 0.4);
            color: white;
        }

        .btn-home {
            flex: 1;
            min-width: 200px;
            padding: 18px;
            background: white;
            color: var(--dark-green);
            border: 2px solid var(--dark-green);
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-home:hover {
            background: var(--dark-green);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 77, 64, 0.2);
        }

        .logout-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
            border-top: 1px solid #e9ecef;
        }

        .logout-footer i {
            color: var(--medium-green);
            margin-right: 5px;
        }

        .time-info {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .time-info i {
            color: var(--accent-gold);
            margin-right: 5px;
        }

        @media (max-width: 576px) {
            .logout-header {
                padding: 40px 30px;
            }

            .logout-body {
                padding: 35px 30px;
            }

            .logout-title {
                font-size: 1.8rem;
            }

            .success-icon {
                width: 100px;
                height: 100px;
            }

            .success-icon i {
                font-size: 3.5rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-reconnect,
            .btn-home {
                width: 100%;
            }
        }

        /* Animation pour les éléments */
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="logout-container">
        <div class="logout-card">
            <div class="logout-header">
                <div class="success-icon">
                    <div class="checkmark-circle"></div>
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="logout-title">Déconnexion Réussie</h1>
                <p class="logout-subtitle">Vous avez été déconnecté en toute sécurité</p>
            </div>

            <div class="logout-body">
                <p class="logout-message fade-in">
                    Votre session a été fermée avec succès. Toutes vos données ont été sécurisées 
                    et votre accès à la plateforme IGOR Banking a été révoqué.
                </p>

                <div class="security-info fade-in" style="animation-delay: 0.2s;">
                    <div class="security-info-title">
                        <i class="fas fa-shield-check"></i>
                        <span>Mesures de sécurité appliquées</span>
                    </div>
                    <div class="security-items">
                        <div class="security-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Session fermée et cookies supprimés</span>
                        </div>
                        <div class="security-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Données temporaires effacées</span>
                        </div>
                        <div class="security-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Connexion sécurisée terminée</span>
                        </div>
                    </div>
                </div>

                <div class="action-buttons fade-in" style="animation-delay: 0.4s;">
                    <a href="login.php?type=agent" class="btn-reconnect">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Se reconnecter</span>
                    </a>
                    <a href="index.php" class="btn-home">
                        <i class="fas fa-home"></i>
                        <span>Accueil</span>
                    </a>
                </div>

                <div class="time-info fade-in" style="animation-delay: 0.6s;">
                    <i class="fas fa-clock"></i>
                    <span>Déconnecté le <?php echo date('d/m/Y à H:i'); ?></span>
                </div>
            </div>

            <div class="logout-footer">
                <i class="fas fa-building"></i>
                <strong>IGOR Banking</strong> - Merci d'avoir utilisé notre plateforme
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Empêcher le retour en arrière après déconnexion
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };

        // Animation de confetti (optionnel - effet visuel)
        document.addEventListener('DOMContentLoaded', function() {
            // Petit effet de confirmation sonore ou visuel peut être ajouté ici
            console.log('Déconnexion réussie - Session terminée');
        });
    </script>
</body>

</html>