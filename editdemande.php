<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

// sécurité (important)
est_connecte();
verifier_acces(['super_admin', 'admin_regional', 'chef_agence', 'agent'], true);

// validation ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: demande.php");
    exit();
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM demande_pret WHERE id = :id");
$stmt->execute(['id' => $id]);
$row = $stmt->fetch();

if (!$row) {
    header("Location: demande.php");
    exit();
}

// traitement formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $montant = $_POST['montant'];
    $duree = $_POST['duree'];
    $taux_interet = $_POST['taux_interet'];
    $statut = $_POST['statut'];

    $sql = "UPDATE demande_pret 
            SET montant = :montant, duree = :duree, taux_interet = :taux_interet, statut = :statut 
            WHERE id = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'montant' => $montant,
        'duree' => $duree,
        'taux_interet' => $taux_interet,
        'statut' => $statut,
        'id' => $id
    ]);

    header("Location: demande.php");
    exit();
}

// 🔥 important
$content = "pages/edit_demande_content.php";
include("layout.php");