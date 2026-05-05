<?php
session_start();
require_once 'config.php';

// Activer les erreurs pour le debug (à enlever en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Protection CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success_message = "";
$error_message = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Erreur de validation CSRF. Veuillez rafraîchir la page et réessayer.");
    }

    // Récupération et nettoyage des données
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';


    // ====================== VÉRIFICATION reCAPTCHA ======================
$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

if (empty($recaptcha_response)) {
    $errors['recaptcha'] = "Veuillez cocher la case « Je ne suis pas un robot ».";
} else {
    $verify_response = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=" . RECAPTCHA_SECRET_KEY . 
        "&response=" . $recaptcha_response . 
        "&remoteip=" . $_SERVER['REMOTE_ADDR']
    );

    $response_data = json_decode($verify_response);

    if (!$response_data->success) {
        $errors['recaptcha'] = "Échec de la vérification reCAPTCHA. Veuillez réessayer.";
    }
}

    // Validation du nom d'utilisateur
    if (strlen($username) < 3) {
        $errors['username'] = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
    } elseif (strlen($username) > 50) {
        $errors['username'] = "Le nom d'utilisateur ne peut pas dépasser 50 caractères.";
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        $errors['username'] = "Le nom d'utilisateur ne peut contenir que des lettres et des chiffres.";
    }

    // Validation email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Veuillez entrer une adresse email valide.";
    } elseif (strlen($email) > 100) {
        $errors['email'] = "L'email ne peut pas dépasser 100 caractères.";
    }

    // Validation mot de passe simplifiée
    if (strlen($password) < 8) {
        $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif (!preg_match('/[a-zA-Z]/', $password)) {
        $errors['password'] = "Le mot de passe doit contenir au moins une lettre.";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors['password'] = "Le mot de passe doit contenir au moins un chiffre.";
    }

    // Confirmation mot de passe
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Les mots de passe ne correspondent pas.";
    }

    // Si pas d'erreurs, vérifier en base
    if (empty($errors)) {
        try {
            // Vérifier si l'email existe déjà
            $check_email = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $check_email->execute([$email]);
            
            if ($check_email->fetchColumn() > 0) {
                $errors['email'] = "Cette adresse email est déjà utilisée.";
            } else {
                // Vérifier si le username existe déjà
                $check_username = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
                $check_username->execute([$username]);
                
                if ($check_username->fetchColumn() > 0) {
                    $errors['username'] = "Ce nom d'utilisateur est déjà pris.";
                } else {
                    // Hachage du mot de passe
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    
                    // Insertion dans la base de données - SANS created_at si la colonne n'existe pas
                    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $result = $stmt->execute([$username, $email, $hashed_password]);
                    
                    if ($result) {
                        // Régénérer le token CSRF
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        
                        $success_message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                        
                        // Stocker l'email pour pré-remplir le formulaire de connexion
                        $_SESSION['registered_email'] = $email;
                    } else {
                        $error_message = "Erreur lors de l'inscription. Veuillez réessayer.";
                    }
                }
            }
        } catch (PDOException $e) {
            // Message d'erreur plus détaillé pour le debug
            $error_message = "Erreur technique : " . $e->getMessage();
            // Log l'erreur pour le debug
            error_log("Erreur register.php: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGOR | Créer un compte</title>
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
            --dark-color: #5a5c69;
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
            background: linear-gradient(135deg, var(--primary-color), #224abe);
            color: white;
            padding: 2rem 1.5rem;
            text-align: center;
            border-bottom: none;
        }

        .card-header h2 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .card-header p {
            opacity: 0.9;
            margin-bottom: 0;
            font-size: 0.95rem;
        }

        .card-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
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
            color: var(--primary-color);
        }

        .form-control {
            border: 1px solid #d1d3e2;
            border-left: none;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: none;
            outline: none;
        }

        .form-control.is-invalid {
            border-color: var(--danger-color);
        }

        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.85rem;
            margin-top: 0.25rem;
            padding-left: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #224abe);
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
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: #d4edda;
            border-left-color: var(--success-color);
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-left-color: var(--danger-color);
            color: #721c24;
        }

        .login-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .login-link:hover {
            color: #224abe;
            text-decoration: underline;
        }

        .security-badge {
            background-color: #f8f9fc;
            border-radius: 5px;
            padding: 0.75rem;
            margin-top: 1rem;
            border-left: 3px solid var(--primary-color);
            font-size: 0.9rem;
        }

        .security-badge i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        .password-requirements {
            font-size: 0.85rem;
            color: var(--secondary-color);
            margin-top: 0.5rem;
            padding: 0.5rem;
            background-color: #f8f9fc;
            border-radius: 5px;
        }

        .password-requirements i {
            margin-right: 0.5rem;
            width: 16px;
        }

        .text-success {
            color: var(--success-color) !important;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user-plus fa-3x mb-3"></i>
                        <h2>Créer un compte</h2>
                        <p>Rejoignez la plateforme IGOR</p>
                    </div>
                    
                    <div class="card-body">
                        <?php if (!empty($success_message)) : ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo htmlspecialchars($success_message); ?>
                                <hr>
                                <a href="login.php" class="alert-link">
                                    <i class="fas fa-sign-in-alt me-1"></i>Se connecter maintenant
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error_message)) : ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($success_message)) : ?>
                        <form method="POST" id="registerForm">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <!-- Nom d'utilisateur -->
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-1"></i>Nom d'utilisateur
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           id="username" 
                                           name="username" 
                                           class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>"
                                           placeholder="Ex: RAKOTO123"
                                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                           required
                                           minlength="3"
                                           maxlength="50"
                                           pattern="[a-zA-Z0-9]+">
                                </div>
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-times-circle me-1"></i><?php echo $errors['username']; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="password-requirements">
                                        <i class="fas fa-info-circle"></i>
                                        Lettres et chiffres uniquement (3-50 caractères)
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Adresse email
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                                           placeholder="exemple@email.com"
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                           required>
                                </div>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-times-circle me-1"></i><?php echo $errors['email']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Mot de passe -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Mot de passe
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>"
                                           placeholder="********"
                                           required
                                           minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-times-circle me-1"></i><?php echo $errors['password']; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="password-requirements">
                                    <div><i class="fas fa-circle" id="req-length"></i> Au moins 8 caractères</div>
                                    <div><i class="fas fa-circle" id="req-letter"></i> Au moins une lettre (A-Z ou a-z)</div>
                                    <div><i class="fas fa-circle" id="req-number"></i> Au moins un chiffre (0-9)</div>
                                </div>
                            </div>

                            <!-- Confirmation mot de passe -->
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Confirmer le mot de passe
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                    <input type="password" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>"
                                           placeholder="********"
                                           required>
                                </div>
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-times-circle me-1"></i><?php echo $errors['confirm_password']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Badge de sécurité -->
                            <div class="security-badge">
                                <i class="fas fa-shield-alt"></i>
                                <strong>Hash bcrypt coût 12</strong> - Sécurité maximale
                            </div>

                            <!-- reCAPTCHA -->
                            <div class="mb-4 text-center">
                                <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
                                <?php if (isset($errors['recaptcha'])): ?>
                                    <div class="invalid-feedback d-block text-center">
                                        <i class="fas fa-times-circle me-1"></i> <?php echo $errors['recaptcha']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Bouton d'inscription -->
                            <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn">
                                <i class="fas fa-user-plus me-2"></i>S'inscrire
                            </button>

                            <!-- Lien connexion -->
                            <p class="text-center mb-0">
                                Vous avez déjà un compte ?
                                <a href="login.php" class="login-link">
                                    <i class="fas fa-sign-in-alt me-1"></i>Se connecter
                                </a>
                            </p>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center mt-4 text-white">
                    <small>&copy; 2023-<?php echo date('Y'); ?> IGOR Banking</small>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            password.type = password.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        // Password checker simplifié
        const password = document.getElementById('password');
        if (password) {
            password.addEventListener('input', function() {
                const value = this.value;
                
                document.getElementById('req-length').className = value.length >= 8 ? 'fas fa-check-circle text-success' : 'fas fa-circle text-secondary';
                document.getElementById('req-letter').className = /[a-zA-Z]/.test(value) ? 'fas fa-check-circle text-success' : 'fas fa-circle text-secondary';
                document.getElementById('req-number').className = /[0-9]/.test(value) ? 'fas fa-check-circle text-success' : 'fas fa-circle text-secondary';
            });
        }

        // Confirm password
        document.getElementById('confirm_password')?.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            if (this.value && this.value !== password) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        // Prevent double submit
        document.getElementById('registerForm')?.addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Inscription...';
        });
    </script>
</body>
</html>