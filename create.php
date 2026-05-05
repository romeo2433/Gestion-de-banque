<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe) 
            VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe)";
    
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'telephone' => $telephone,
        'mot_de_passe' => $mot_de_passe
    ]);

    // ✅ REDIRECTION ICI
    header("Location: utilisateur.php?success=add");
    exit();

} catch (PDOException $e) {
    echo "Erreur d'insertion : " . $e->getMessage();
    exit();
}


    }
    
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Client</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2 class="text-center">Ajouter un Client</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Nom :</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Prénom :</label>
            <input type="text" name="prenom" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email :</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Téléphone :</label>
            <input type="text" name="telephone" class="form-control">
        </div>
        <div class="mb-3">
            <label>Mot de passe :</label>
            <input type="password" name="mot_de_passe" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
        <a href="utilisateur.php" class="btn btn-secondary">Retour</a>
    </form>
</body>
</html>
