<?php
session_start();
require_once 'config.php';

// (optionnel selon ton système)
require_once 'verification_role.php';
est_connecte();

$errors = [];

if (isset($_GET['id'])) {

    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id === false) {
        header("Location: utilisateur.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error'] = "Utilisateur non trouvé";
        header("Location: utilisateur.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $telephone = trim($_POST['telephone']);

        if (empty($nom)) $errors[] = "Nom obligatoire";
        if (empty($prenom)) $errors[] = "Prénom obligatoire";

        if (empty($email)) {
            $errors[] = "Email obligatoire";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide";
        }

        if (empty($errors)) {
            $check = $conn->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
            $check->execute(['email' => $email, 'id' => $id]);

            if ($check->fetch()) {
                $errors[] = "Email déjà utilisé";
            }
        }

        if (empty($errors)) {
            try {

                if (!empty($_POST['mot_de_passe'])) {

                    if (strlen($_POST['mot_de_passe']) < 8) {
                        $errors[] = "Mot de passe trop court";
                    } else {
                        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

                        $sql = "UPDATE users SET nom=?, prenom=?, email=?, telephone=?, mot_de_passe=? WHERE id=?";
                        $params = [$nom, $prenom, $email, $telephone, $mot_de_passe, $id];
                    }

                } else {
                    $sql = "UPDATE users SET nom=?, prenom=?, email=?, telephone=? WHERE id=?";
                    $params = [$nom, $prenom, $email, $telephone, $id];
                }

                if (empty($errors)) {
                    $stmt = $conn->prepare($sql);
                    $stmt->execute($params);

                    $_SESSION['success'] = "Utilisateur modifié";
                    header("Location: utilisateur.php");
                    exit();
                }

            } catch (PDOException $e) {
                $errors[] = "Erreur DB";
            }
        }
    }

} else {
    header("Location: utilisateur.php");
    exit();
}

// 🔥 IMPORTANT
$content = "pages/edit_user_content.php";
include("layout.php");