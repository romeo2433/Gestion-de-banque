<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $token = bin2hex(random_bytes(50));
        $stmt = $conn->prepare("UPDATE users SET reset_token = :token, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = :email");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $reset_link = "http://yourwebsite.com/reset_password.php?token=$token";
        mail($email, "Réinitialisation du mot de passe", "Cliquez sur ce lien pour réinitialiser votre mot de passe: $reset_link");
        
        $message = "Un email de réinitialisation a été envoyé.";
    } else {
        $message = "Aucun compte associé à cet email.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2 class="mb-4 text-center">Mot de passe oublié</h2>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"> <?= htmlspecialchars($message) ?> </div>
    <?php endif; ?>
    
    <form method="POST" class="w-50 mx-auto">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Envoyer</button>
    </form>
</body>
</html>
