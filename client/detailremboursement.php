<?php
session_start();
require_once '../config.php';

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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du remboursement</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 30px;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,.1);
        }

        h2 {
            margin-bottom: 25px;
        }

        .ligne {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="card">

        <h2>📄 Détails du remboursement</h2>

        <div class="ligne">
            <span class="label">ID :</span>
            <?= htmlspecialchars($remboursement['id']) ?>
        </div>

        <div class="ligne">
            <span class="label">Montant :</span>
            <?= number_format($remboursement['montant'], 0, ',', ' ') ?> Ar
        </div>

        <?php if (isset($remboursement['date_paiement'])): ?>
        <div class="ligne">
            <span class="label">Date de paiement :</span>
            <?= htmlspecialchars($remboursement['date_paiement']) ?>
        </div>
        <?php endif; ?>

        <?php if (isset($remboursement['date_fin'])): ?>
        <div class="ligne">
            <span class="label">Date fin remboursement :</span>
            <?= htmlspecialchars($remboursement['date_fin']) ?>
        </div>
        <?php endif; ?>

        <?php if (isset($remboursement['mode_paiement'])): ?>
        <div class="ligne">
            <span class="label">Mode de paiement :</span>
            <?= htmlspecialchars($remboursement['mode_paiement']) ?>
        </div>
        <?php endif; ?>

        <?php if (isset($remboursement['statut'])): ?>
        <div class="ligne">
            <span class="label">Statut :</span>
            <?= htmlspecialchars($remboursement['statut']) ?>
        </div>
        <?php endif; ?>

        <a href="../espace_client.php" class="btn">
            ← Retour
        </a>

    </div>

</div>

</body>
</html>