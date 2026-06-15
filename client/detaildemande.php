<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT dp.*, u.username, u.nom, u.prenom, u.email
    FROM demande_pret dp
    JOIN users u ON dp.utilisateur_id = u.id
    WHERE dp.id = :id
");

$stmt->execute(['id' => $id]);
$demande = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Détails prêt</title>
</head>
<body>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 30px;
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
}

.card {
    max-width: 700px;
    margin: auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-body {
    padding: 30px;
}

.card-body p {
    margin: 15px 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
    font-size: 16px;
}

.card-body strong {
    color: #555;
    display: inline-block;
    min-width: 120px;
}

.btn-retour {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    background: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: 0.3s;
}

.btn-retour:hover {
    background: #5a6268;
}
</style>
<h2>Détails du prêt</h2>

<div class="card">
    <div class="card-body">
        <p><strong>Client :</strong> <?= htmlspecialchars($demande['username']) ?></p>
        <p><strong>Nom :</strong> <?= htmlspecialchars($demande['nom'].' '.$demande['prenom']) ?></p>
        <p><strong>Montant :</strong> <?= number_format($demande['montant'],0,',',' ') ?> Ar</p>
        <p><strong>Durée :</strong> <?= $demande['duree'] ?> mois</p>
        <p><strong>Taux :</strong> <?= $demande['taux_interet'] ?> %</p>
        <a href="../espace_client.php">Retour</a>
    </div>
</div>

</body>
</html>
