<?php
session_start();
require_once '../config.php';
require_once '../verification_role.php';

est_connecte();

// 🔴 ID obligatoire
if (!isset($_GET['id'])) {
    header("Location: demande.php");
    exit;
}

$id = intval($_GET['id']);

// 🔹 MESSAGE
$success = '';
$error = '';

try {

    // =========================
    // 🔹 UPDATE (POST)
    // =========================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $montant = floatval($_POST['montant']);
        $duree = intval($_POST['duree']);
        $taux_interet = floatval($_POST['taux_interet']);

        $update = $conn->prepare("
            UPDATE demande_pret
            SET montant = :montant,
                duree = :duree,
                taux_interet = :taux_interet
            WHERE id = :id
        ");

        $update->execute([
            'montant' => $montant,
            'duree' => $duree,
            'taux_interet' => $taux_interet,
            'id' => $id
        ]);

        $success = "✔ Modification réussie !";
    }

    // =========================
    // 🔹 SELECT (GET + refresh)
    // =========================
    $stmt = $conn->prepare("
        SELECT *
        FROM demande_pret
        WHERE id = :id
    ");

    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        die("Demande introuvable.");
    }

} catch (PDOException $e) {
    $error = "Erreur : " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier demande</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>

<div class="form-container">

    <h2 class="form-title">Modifier une Demande de Prêt</h2>

    <!-- 🔥 MESSAGE -->
    <?php if ($success): ?>
        <div style="color: green; padding: 10px;">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div style="color: red; padding: 10px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- 🔹 FORM -->
    <form method="POST">

        <div class="form-group">
            <label>Montant (€)</label>
            <input type="number"
                   name="montant"
                   class="modern-input"
                   value="<?= htmlspecialchars($row['montant']) ?>"
                   required>
        </div>

        <div class="form-grid">

            <div class="form-group">
                <label>Durée (mois)</label>
                <input type="number"
                       name="duree"
                       class="modern-input"
                       value="<?= htmlspecialchars($row['duree']) ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Taux d'intérêt (%)</label>
                <input type="number"
                       step="0.01"
                       name="taux_interet"
                       class="modern-input"
                       value="<?= htmlspecialchars($row['taux_interet']) ?>"
                       required>
            </div>

        </div>

        <div class="btn-group" style="display:flex; gap:12px; margin-top:32px;">
            <button type="submit" class="btn btn-success">
                Mettre à jour
            </button>

            <a href="../espace_client.php" class="btn btn-secondary">
                Retour
            </a>
        </div>

    </form>

</div>

</body>
</html>