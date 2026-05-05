<?php
session_start();
require 'config.php';

// Génération CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";
$errors = [];

/**
 * Fonction création Super Admin
 */
function createSuperAdmin($conn, $data, &$errors) {

    //  Code secret (à activer si besoin)
    $SECRET_CODE = "CREATE_SUPER_ADMIN_IGOR";

    $username = trim($data['username']);
    $email = trim($data['email']);
    $password = $data['password'];
    $confirm = $data['confirm'];
    //$code = $data['secret_code'] ?? '';

    // Validation
    if (strlen($username) < 3) {
        $errors[] = "Nom trop court";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide";
    }

    if (strlen($password) < 8) {
        $errors[] = "Mot de passe trop court";
    }

    if ($password !== $confirm) {
        $errors[] = "Mot de passe non identique";
    }

    //  Vérifier code secret (optionnel)
    /*
    if ($code !== $SECRET_CODE) {
        $errors[] = "Code secret incorrect";
    }
    */

    // 🔎 Vérifier doublon email
    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $errors[] = "Email déjà utilisé";
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("
            INSERT INTO users (username, email, password, role)
            VALUES (?, ?, ?, 'super_admin')
        ");

        if ($stmt->execute([$username, $email, $hash])) {
            return "Super Admin créé avec succès ";
        } else {
            $errors[] = "Erreur insertion";
        }
    }

    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Erreur CSRF");
    }

    $message = createSuperAdmin($conn, $_POST, $errors);

    if ($message) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Créer Super Admin</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            transition: 0.3s;
        }

        input:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #45a049;
        }

        .error {
            background: #ffe6e6;
            color: #d8000c;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .success {
            background: #e6ffed;
            color: #2e7d32;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

<div class="container">

<h2>Créer Super Admin</h2>

<!-- 🔴 Erreurs -->
<?php foreach ($errors as $e): ?>
    <div class="error"><?php echo htmlspecialchars($e); ?></div>
<?php endforeach; ?>

<!-- 🟢 Succès -->
<?php if ($message): ?>
    <div class="success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <input type="password" name="confirm" placeholder="Confirmer" required>

    <button type="submit">Créer Super Admin</button>
</form>

</div>

</body>
</html>