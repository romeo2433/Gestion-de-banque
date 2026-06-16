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

try {

    // Récupération des données
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $nom       = trim($_POST['nom'] ?? '');
    $prenom    = trim($_POST['prenom'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');

    // Vérification minimale
    if (empty($username) || empty($email)) {
        die("Le nom d'utilisateur et l'email sont obligatoires.");
    }

    // Récupérer l'ancienne photo
    $stmt = $conn->prepare("
        SELECT photo
        FROM users
        WHERE id = :id
    ");

    $stmt->execute([
        'id' => $user_id
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $photoName = $user['photo'] ?? null;

    // Upload photo
    if (
        isset($_FILES['photo']) &&
        $_FILES['photo']['error'] === UPLOAD_ERR_OK
    ) {

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        $extension = strtolower(
            pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $allowed)) {
            die("Format d'image non autorisé.");
        }

        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        $photoName = time() . "_" . uniqid() . "." . $extension;

        move_uploaded_file(
            $_FILES['photo']['tmp_name'],
            "uploads/" . $photoName
        );
    }

    // Mise à jour du profil
    $sql = "
        UPDATE users
        SET
            username = :username,
            email = :email,
            nom = :nom,
            prenom = :prenom,
            telephone = :telephone,
            photo = :photo,
            updated_at = NOW()
        WHERE id = :id
    ";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        'username'  => $username,
        'email'     => $email,
        'nom'       => $nom,
        'prenom'    => $prenom,
        'telephone' => $telephone,
        'photo'     => $photoName,
        'id'        => $user_id
    ]);

    // Mise à jour de la session si nécessaire
    $_SESSION['username'] = $username;

    header("Location: profil.php?success=1");
    exit;

} catch (PDOException $e) {

    die("Erreur : " . $e->getMessage());

}