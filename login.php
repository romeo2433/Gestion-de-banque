<?php
session_start();
require_once 'config.php';


// Importation an ilay fichier mampadefa an ilay PHPMail ni integrerna tao am dossier 
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
 // Miantso bibliotheque ilay vao nampidirina  
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ====================== CONFIGURATION ======================
// Mijery Role ampisehona ilay formulaire hoe admin io sa client tokony iconnecte 
$selected_role = $_GET['role'] ?? $_SESSION['selected_role'] ?? 'client';
$_SESSION['selected_role'] = $selected_role;

// ====================== GESTION DU STEP ======================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Verification isaky miverina am page anakiray ao am login hoe iza marina io mi connecte io 
    unset($_SESSION['login_step'], $_SESSION['temp_user_id'], $_SESSION['temp_email']);
    $step = 1;
} else {
    $step = $_SESSION['login_step'] ?? 1;
}

$error = '';
$success_msg = '';

// ====================== TRAITEMENT POST ======================
//verification mijery hoe ampy ve le champ nofenona na hoe marina ve sa diso 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($step == 1) {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
    
        if (empty($email) || empty($password)) {
            $error = "Veuillez remplir tous les champs.";
        } else {
            $result = loginStep1($email, $password, $selected_role);
    
            if ($result['success']) {
                $_SESSION['user_id'] = $result['user_id'];
                $_SESSION['role']    = $selected_role;
                $_SESSION['email']   = $email;
    
                // Différenciation Client / Super Admin
                if ($selected_role === 'super_admin') {
                    // Connexion directe sans OTP
                    unset($_SESSION['login_step'], $_SESSION['temp_user_id'], $_SESSION['temp_email']);
                    header("Location: dashboard.php");
                    exit();
                } else {
                    // Client → On passe à l'OTP
                    $_SESSION['temp_user_id'] = $result['user_id'];
                    $_SESSION['temp_email']   = $email;
                    $_SESSION['login_step']   = 2;
    
                    sendOTP($email, $result['user_id']);
                    $success_msg = "Un code de vérification a été envoyé à votre adresse email.";
                    $step = 2;
                }
            } else {
                $error = $result['message'];
            }
        }
    }
    elseif ($step == 2) {
        $otp_entered = trim($_POST['otp'] ?? '');

        if (empty($otp_entered)) {
            $error = "Veuillez entrer le code OTP.";
        } else {
            $result = verifyOTP($_SESSION['temp_email'] ?? '', $otp_entered);

            if ($result['success']) {
                $_SESSION['user_id'] = $_SESSION['temp_user_id'];
                $_SESSION['role']    = 'client';
                $_SESSION['email']   = $_SESSION['temp_email'];

                unset($_SESSION['temp_user_id'], $_SESSION['temp_email'], $_SESSION['login_step']);

                header("Location: espace_client.php");
                exit();
            } else {
                $error = $result['message'];
            }
        }
    }
}
//Function mampilamina afera
function loginStep1($email, $password, $required_role) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => "Aucun compte trouvé avec cet email."];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => "Mot de passe incorrect."];
        }

        if ($user['role'] !== $required_role) {
            return ['success' => false, 'message' => "Accès refusé pour ce rôle."];
        }

        return ['success' => true, 'user_id' => $user['id']];

    } catch (Exception $e) {
        return ['success' => false, 'message' => "Erreur technique."];
    }
}

function sendOTP($email, $user_id) {
    global $conn;

    $otp = rand(100000, 999999);
    $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    $stmt = $conn->prepare("UPDATE users SET otp = ?, otp_expires_at = ? WHERE id = ?");
    $stmt->execute([$otp, $expires_at, $user_id]);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'romeomahefaromeo@gmail.com';
        $mail->Password   = 'xpzcswqnhjhaacnm';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('romeomahefaromeo@gmail.com', 'IGOR PRO');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = "Code de vérification IGOR";
        $mail->Body    = "<h3>Votre code OTP : <strong>$otp</strong></h3><p>Expire dans 15 minutes.</p>";

        $mail->send();
    } catch (Exception $e) {
        error_log("Erreur PHPMailer : " . $e->getMessage());
        // Ra misy erreur 
         $GLOBALS['error'] = "Erreur d'envoi de l'email. Veuillez réessayer.";
    }
}

function verifyOTP($email, $otp_entered) {
    global $conn;

    $stmt = $conn->prepare("SELECT otp, otp_expires_at FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || empty($user['otp'])) {
        return ['success' => false, 'message' => "Aucun code OTP valide trouvé."];
    }

    if (strtotime($user['otp_expires_at']) < time()) {
        return ['success' => false, 'message' => "Le code OTP a expiré. Veuillez recommencer."];
    }

    if ($user['otp'] !== $otp_entered) {
        return ['success' => false, 'message' => "Code OTP incorrect."];
    }

    $stmt = $conn->prepare("UPDATE users SET otp = NULL, otp_expires_at = NULL WHERE email = ?");
    $stmt->execute([$email]);

    return ['success' => true];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGOR PRO | Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --admin-color: #1e3a8a;
            --client-color: #0d9488;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .card-header {
            background: <?php 
                if (isset($_SESSION['selected_role']) && $_SESSION['selected_role'] == 'super_admin') {
                    echo "linear-gradient(135deg, var(--admin-color), #0f172a)";
                } elseif (isset($_SESSION['selected_role']) && $_SESSION['selected_role'] == 'client') {
                    echo "linear-gradient(135deg, var(--client-color), #115e59)";
                } else {
                    echo "linear-gradient(135deg, var(--primary-color), #224abe)";
                }
            ?>;
            color: white;
            padding: 2rem 1.5rem;
            text-align: center;
            border-bottom: none;
        }
        .card-header i {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        .card-header h2 {
            font-weight: 600;
            margin: 0.5rem 0 0;
        }
        .role-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            background: rgba(255,255,255,0.2);
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
        .role-badge i {
            font-size: 0.9rem;
            margin-right: 0.3rem;
        }
        .card-body {
            padding: 2rem;
        }
        .btn-primary {
            background: <?php 
                if (isset($_SESSION['selected_role']) && $_SESSION['selected_role'] == 'super_admin') {
                    echo "linear-gradient(135deg, var(--admin-color), #0f172a)";
                } elseif (isset($_SESSION['selected_role']) && $_SESSION['selected_role'] == 'client') {
                    echo "linear-gradient(135deg, var(--client-color), #115e59)";
                } else {
                    echo "linear-gradient(135deg, var(--primary-color), #224abe)";
                }
            ?>;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        }
        .alert {
            border-radius: 10px;
            border-left-width: 4px;
        }
        .input-group {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .input-group-text {
            background-color: #f8f9fc;
            border: 1px solid #d1d3e2;
            border-right: none;
            color: <?php 
                if (isset($_SESSION['selected_role']) && $_SESSION['selected_role'] == 'super_admin') {
                    echo "var(--admin-color)";
                } elseif (isset($_SESSION['selected_role']) && $_SESSION['selected_role'] == 'client') {
                    echo "var(--client-color)";
                } else {
                    echo "var(--primary-color)";
                }
            ?>;
        }
        .form-control {
            border: 1px solid #d1d3e2;
            border-left: none;
            padding: 0.75rem 1rem;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: none;
            outline: none;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .back-link:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header">
                        <?php if (isset($_SESSION['selected_role']) && $_SESSION['selected_role'] == 'super_admin'): ?>
                            <i class="fas fa-crown"></i>
                            <h2>IGOR PRO</h2>
                            <div class="role-badge">
                                <i class="fas fa-shield-alt"></i> Connexion Super Admin
                            </div>
                        <?php elseif (isset($_SESSION['selected_role']) && $_SESSION['selected_role'] == 'client'): ?>
                            <i class="fas fa-user-circle"></i>
                            <h2>IGOR PRO</h2>
                            <div class="role-badge">
                                <i class="fas fa-user"></i> Espace Client
                            </div>
                        <?php else: ?>
                            <i class="fas fa-university"></i>
                            <h2>IGOR PRO</h2>
                            <p class="mb-0 opacity-75">Plateforme bancaire professionnelle</p>
                        <?php endif; ?>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($success_msg)): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= htmlspecialchars($success_msg) ?>
                            </div>
                        <?php endif; ?>

                        <!-- ====================== ÉTAPE 1 : Email + Mot de passe ====================== -->
                        <?php if ($step == 1): ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Adresse email</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" name="email" class="form-control"
                                               placeholder="exemple@email.com" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Mot de passe</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" name="password" class="form-control"
                                               placeholder="********" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    <?php if ($_SESSION['selected_role'] == 'super_admin'): ?>
                                        Accéder au Dashboard Admin
                                    <?php elseif ($_SESSION['selected_role'] == 'client'): ?>
                                        Accéder à mon Espace
                                    <?php else: ?>
                                        Se connecter
                                    <?php endif; ?>
                                </button>
                                    <?php if ($_SESSION['selected_role'] == 'client'): ?>
                                        <div class="text-center mt-4">
                                            <p class="mb-1 text-muted">Pas encore inscrit ?</p>
                                            <a href="register.php" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-user-plus me-1"></i>Créer un compte
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                
                            </form>

                        <?php elseif ($step == 2): ?>
                            <div class="text-center mb-4">
                                <h5>Vérification de sécurité</h5>
                                <p class="text-muted">
                                    Un code à 6 chiffres a été envoyé à :<br>
                                    <strong><?= htmlspecialchars($_SESSION['temp_email'] ?? '') ?></strong>
                                </p>
                            </div>

                            <form method="POST">
                                <div class="mb-4">
                                    <label class="form-label text-center d-block">Code OTP</label>
                                    <input type="text" name="otp" maxlength="6" 
                                           class="form-control text-center fs-3 fw-bold"
                                           placeholder="000000" required autofocus>
                                </div>

                                <button type="submit" class="btn btn-success w-100 py-3 mb-3">
                                    <i class="fas fa-check me-2"></i>Vérifier le code
                                </button>
                            </form>

                            <div class="text-center">
                                <a href="login.php?type=agent" class="text-danger small">
                                    ← Retour à la connexion
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- Lien retour (visible seulement à l'étape 1) -->
                        <?php if ($step == 1): ?>
                            <a href="index.php" class="back-link d-block text-center mt-3">
                                <i class="fas fa-arrow-left me-1"></i>Retour à la sélection des rôles
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>