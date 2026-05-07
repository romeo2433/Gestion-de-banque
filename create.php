<?php
session_start();
include 'config.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $telephone = trim($_POST['telephone']);
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

        if (empty($nom) || empty($prenom) || empty($email)) {
            throw new Exception("Champs obligatoires manquants");
        }

        $sql = "INSERT INTO users 
                (nom, prenom, email, telephone, mot_de_passe) 
                VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'mot_de_passe' => $mot_de_passe
        ]);

        $success = "Utilisateur ajouté avec succès";

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Charger les utilisateurs
$stmt = $conn->query("SELECT * FROM users ORDER BY id DESC");
$utilisateurs = $stmt->fetchAll();

$content = "pages/create_content.php";
include "layout.php";