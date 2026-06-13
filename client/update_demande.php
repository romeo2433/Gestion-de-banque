<?php
session_start();
require_once '../config.php';
require_once '../verification_role.php';

est_connecte();

// 🔴 ID
if (!isset($_GET['id'])) {
    header("Location: demande.php");
    exit;
}

$id = intval($_GET['id']);

// 🔴 POST only


try {

    // 🔹 données du form
    $montant = floatval($_POST['montant']);
    $duree = intval($_POST['duree']);
    $taux_interet = floatval($_POST['taux_interet']);

    // ❌ STATUT SUPPRIMÉ (IMPORTANT)

    // 🔹 UPDATE SANS statut
    $stmt = $conn->prepare("
        UPDATE demande_pret
        SET montant = :montant,
            duree = :duree,
            taux_interet = :taux_interet
        WHERE id = :id
    ");

    $stmt->execute([
        'montant' => $montant,
        'duree' => $duree,
        'taux_interet' => $taux_interet,
        'id' => $id
    ]);

 

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}