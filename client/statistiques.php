<?php
session_start();
require_once '../config.php';
require_once '../verification_role.php';

est_connecte();

$user_id = $_SESSION['user_id'];

try {

    // Statistiques générales
    $stmt = $conn->prepare("
        SELECT
            COUNT(*) AS total_demandes,
            SUM(CASE WHEN statut = 'en attente' THEN 1 ELSE 0 END) AS en_attente,
            SUM(CASE WHEN statut = 'approuvé' THEN 1 ELSE 0 END) AS approuve,
            SUM(CASE WHEN statut = 'refusé' THEN 1 ELSE 0 END) AS refuse,
            SUM(CASE WHEN statut = 'approuvé' THEN montant ELSE 0 END) AS montant_approuve
        FROM demande_pret
        WHERE utilisateur_id = :user_id
    ");

    $stmt->execute([
        'user_id' => $user_id
    ]);

    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes statistiques</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body{
            font-family: Arial, sans-serif;
            background:#f5f7fa;
            padding:20px;
        }

        h2{
            text-align:center;
            margin-bottom:30px;
        }

        .cards{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
            gap:20px;
            margin-bottom:40px;
        }

        .card{
            background:white;
            padding:20px;
            border-radius:12px;
            box-shadow:0 2px 8px rgba(0,0,0,.1);
            text-align:center;
        }

        .card h3{
            margin:0;
            color:#666;
            font-size:16px;
        }

        .card p{
            margin-top:10px;
            font-size:28px;
            font-weight:bold;
        }

        .chart-container{
            background:white;
            padding:20px;
            border-radius:12px;
            box-shadow:0 2px 8px rgba(0,0,0,.1);
            max-width:800px;
            margin:auto;
        }

        .btn-retour{
            display:inline-block;
            margin-top:20px;
            padding:10px 20px;
            background:#6c757d;
            color:white;
            text-decoration:none;
            border-radius:6px;
        }
    </style>
</head>
<body>

<h2>📊 Mes statistiques de prêts</h2>

<div class="cards">

    <div class="card">
        <h3>Total demandes</h3>
        <p><?= $stats['total_demandes'] ?></p>
    </div>

    <div class="card">
        <h3>En attente</h3>
        <p><?= $stats['en_attente'] ?></p>
    </div>

    <div class="card">
        <h3>Approuvées</h3>
        <p><?= $stats['approuve'] ?></p>
    </div>

    <div class="card">
        <h3>Refusées</h3>
        <p><?= $stats['refuse'] ?></p>
    </div>

    <div class="card">
        <h3>Montant approuvé</h3>
        <p><?= number_format($stats['montant_approuve'], 0, ',', ' ') ?> Ar</p>
    </div>

</div>

<div class="chart-container">
    <canvas id="statutChart"></canvas>
</div>

<div style="text-align:center;">
    <a href="../espace_client.php" class="btn-retour">
        ← Retour
    </a>
</div>

<script>
const ctx = document.getElementById('statutChart');

new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [
            'En attente',
            'Approuvées',
            'Refusées'
        ],
        datasets: [{
            data: [
                <?= (int)$stats['en_attente'] ?>,
                <?= (int)$stats['approuve'] ?>,
                <?= (int)$stats['refuse'] ?>
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Répartition des demandes'
            }
        }
    }
});
</script>

</body>
</html>