<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

est_connecte();

$id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT
        dp.*,
        u.username,
        u.nom,
        u.prenom,
        u.email
    FROM demande_pret dp
    JOIN users u
        ON dp.utilisateur_id = u.id
    WHERE dp.id = :id
");

$stmt->execute(['id' => $id]);

$demande = $stmt->fetch(PDO::FETCH_ASSOC);

$content = "pages/detaildemande_content.php";
include("layout.php");