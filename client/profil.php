<?php
session_start();
require_once '../config.php';
//require_once 'verification_role.php';

//est_connecte();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT *
    FROM users
    WHERE id = :id
");

$stmt->execute([
    'id' => $user_id
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .profile-card {
            max-width: 800px;
            margin: 30px auto;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #0d6efd;
        }
    </style>
</head>
<body class="bg-light">

<div class="container">

    <div class="card shadow profile-card">

        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">👤 Mon Profil</h3>
        </div>

        <div class="card-body">

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    Profil mis à jour avec succès.
                </div>
            <?php endif; ?>

            <div class="text-center mb-4">

                <?php
                $photo = !empty($user['photo'])
                    ? "uploads/" . $user['photo']
                    : "uploads/default.png";
                ?>

                <img src="<?= htmlspecialchars($photo) ?>"
                     class="profile-photo"
                     alt="Photo de profil">

                <h4 class="mt-3">
                    <?= htmlspecialchars($user['username']) ?>
                </h4>

                <p class="text-muted">
                    <?= htmlspecialchars($user['role']) ?>
                </p>

            </div>

            <form method="POST"
                  action="update_profil.php"
                  enctype="multipart/form-data">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom d'utilisateur</label>
                        <input type="text"
                               name="username"
                               class="form-control"
                               value="<?= htmlspecialchars($user['username']) ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               value="<?= htmlspecialchars($user['email']) ?>">
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text"
                               name="nom"
                               class="form-control"
                               value="<?= htmlspecialchars($user['nom']) ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text"
                               name="prenom"
                               class="form-control"
                               value="<?= htmlspecialchars($user['prenom']) ?>">
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nouvelle photo</label>
                        <input type="file"
                               name="photo"
                               class="form-control"
                               accept="image/*">
                    </div>

                </div>

                <div class="d-grid gap-2 mt-3">

                    <button type="submit" class="btn btn-success">
                        💾 Enregistrer les modifications
                    </button>

                    <a href="../espace_client.php" class="btn btn-secondary">
                        ↩ Retour au tableau de bord
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>