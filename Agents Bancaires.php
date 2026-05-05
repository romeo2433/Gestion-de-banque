<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

est_connecte();
verifier_acces(['super_admin', 'admin_regional'], true);

$user_agence_id = $_SESSION['agence_id'] ?? null;
$success_message = '';
$error_message = '';

/* =========================
   AJOUT AGENT
========================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter'])) {

    try {
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $telephone = trim($_POST['telephone']);
        $date_embauche = $_POST['date_embauche'] ?? null;

        if ($nom == '' || $prenom == '' || $email == '') {
            throw new Exception("Champs obligatoires manquants");
        }

        // agence
        if ($_SESSION['role'] == 'admin_regional') {
            $stmt = $conn->prepare("SELECT nom FROM agences WHERE id_agence=?");
            $stmt->execute([$user_agence_id]);
            $agence = $stmt->fetchColumn();
        } else {
            $agence = $_POST['agence'] ?? null;
        }

        // insert
        $sql = "INSERT INTO agents_bancaires 
                (nom, prenom, email, telephone, date_embauche, agence)
                VALUES (:nom, :prenom, :email, :telephone, :date_embauche, :agence)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'date_embauche' => $date_embauche,
            'agence' => $agence
        ]);

        $success_message = "Agent ajouté avec succès";

    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

/* =========================
   DELETE AGENT
========================= */
if (isset($_GET['supprimer'])) {

    $id = (int) $_GET['supprimer'];

    $stmt = $conn->prepare("DELETE FROM agents_bancaires WHERE id=?");
    $stmt->execute([$id]);

    $success_message = "Agent supprimé";
}

/* =========================
   LISTE AGENTS
========================= */
if ($_SESSION['role'] == 'admin_regional') {

    $stmt = $conn->prepare("SELECT * FROM agents_bancaires WHERE agence = (
        SELECT nom FROM agences WHERE id_agence = ?
    )");

    $stmt->execute([$user_agence_id]);

} else {
    $stmt = $conn->query("SELECT * FROM agents_bancaires");
}

$agents = $stmt->fetchAll();

/* agences */
$agences = $conn->query("SELECT * FROM agences")->fetchAll();

$content = "pages/agents_content.php";
include "layout.php";