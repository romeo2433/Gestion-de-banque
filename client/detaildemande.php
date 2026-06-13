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
