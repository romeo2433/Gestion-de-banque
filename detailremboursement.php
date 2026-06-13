<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT *
    FROM remboursement
    WHERE id = :id
    AND utilisateur_id = :utilisateur_id
");

$stmt->execute([
    'id' => $id,
    'utilisateur_id' => $_SESSION['user_id']
]);

$remboursement = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$remboursement) {
    die("Remboursement introuvable.");
}

$content = "pages/detailremboursement_content.php";
include("layout.php");