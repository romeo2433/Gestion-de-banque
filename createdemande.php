<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $utilisateur_id = $_POST['utilisateur_id'];
    $montant = $_POST['montant'];
    $duree = $_POST['duree'];
    $taux_interet = $_POST['taux_interet'];
    $statut = $_POST['statut'];

    // Insertion sécurisée
    $sql = "INSERT INTO demande_pret (utilisateur_id, montant, duree, taux_interet, statut) 
            VALUES (:utilisateur_id, :montant, :duree, :taux_interet, :statut)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'utilisateur_id' => $utilisateur_id,
        'montant' => $montant,
        'duree' => $duree,
        'taux_interet' => $taux_interet,
        'statut' => $statut
    ]);
header("Location: demande.php?success=2");
exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter une Demande de Prêt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Ajouter une Demande de Prêt</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Utilisateur</label>
            <select name="utilisateur_id" class="form-control" required>
                <option value="">Sélectionner un utilisateur</option>
                <?php
                $stmt = $conn->query("SELECT id, nom, prenom FROM utilisateurs");
                $utilisateurs = $stmt->fetchAll();
                foreach ($utilisateurs as $utilisateur) {
                    echo "<option value='{$utilisateur['id']}'>{$utilisateur['nom']} {$utilisateur['prenom']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Montant</label>
            <input type="number" name="montant" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Durée (mois)</label>
            <input type="number" name="duree" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Taux d'Intérêt</label>
            <input type="number" step="0.01" name="taux_interet" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Statut</label>
            <select name="statut" class="form-control" required>
                <option value="en attente">En Attente</option>
                <option value="approuvé">Approuvé</option>
                <option value="refusé">Refusé</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
        <a href="demande.php" class="btn btn-secondary">Retour</a>
    </form>
</body>
</html>
