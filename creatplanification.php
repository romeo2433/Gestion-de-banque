<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $statut = $_POST['statut'];

    // Convertir les dates au format attendu par la base de données
    $date_debut = date('Y-m-d H:i:s', strtotime($date_debut));
    $date_fin = date('Y-m-d H:i:s', strtotime($date_fin));

    $sql = "INSERT INTO planification (titre, description, date_debut, date_fin, statut) 
            VALUES (:titre, :description, :date_debut, :date_fin, :statut)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'titre' => $titre,
        'description' => $description,
        'date_debut' => $date_debut,
        'date_fin' => $date_fin,
        'statut' => $statut
    ]);

    // Rediriger après l'insertion
    header("Location: planification.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Planification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Ajouter une Planification</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Titre :</label>
            <input type="text" name="titre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description :</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Date de Début :</label>
            <input type="datetime-local" name="date_debut" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Date de Fin :</label>
            <input type="datetime-local" name="date_fin" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Statut :</label>
            <select name="statut" class="form-control" required>
                <option value="en attente">En Attente</option>
                <option value="terminé">Terminé</option>
                <option value="en cours">En Cours</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
        <a href="planification.php" class="btn btn-secondary">Retour</a>
    </form>
</body>
</html>
