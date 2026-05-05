<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

est_connecte();
verifier_acces(['super_admin','admin_regional','chef_agence','agent'], true);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['error'] = "ID invalide";
    header("Location: remboursement.php");
    exit();
}

/* =======================
   UPDATE (POST)
======================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $id = $_POST['id'];
        $utilisateur_id = $_POST['utilisateur_id'];
        $montant = $_POST['montant'];
        $date_remboursement = $_POST['date_remboursement'];
        $statut = $_POST['statut'];

        $sql = "UPDATE remboursement SET 
                utilisateur_id = :utilisateur_id,
                montant = :montant,
                date_remboursement = :date_remboursement,
                statut = :statut
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'utilisateur_id' => $utilisateur_id,
            'montant' => $montant,
            'date_remboursement' => $date_remboursement,
            'statut' => $statut,
            'id' => $id
        ]);

        $_SESSION['message'] = "Remboursement modifié avec succès";
        header("Location: remboursement.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

/* =======================
   DATA FOR VIEW
======================= */

// remboursement
$stmt = $conn->prepare("SELECT * FROM remboursement WHERE id = :id");
$stmt->execute(['id' => $id]);
$remboursement = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$remboursement) {
    $_SESSION['error'] = "Remboursement introuvable";
    header("Location: remboursement.php");
    exit();
}

// users
$stmt = $conn->query("SELECT id, nom, prenom FROM users ORDER BY nom");
$utilisateurs = $stmt->fetchAll();

$content = "pages/editremboursement_content.php";
include "layout.php";