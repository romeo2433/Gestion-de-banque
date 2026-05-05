<?php
session_start();
if (!isset($_SESSION['user_id'])) 
    header("Location: login.php");
    exit();

// Ajout d'un agent
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter_agent'])) {
    $stmt = $pdo->prepare("INSERT INTO agents_bancaires (nom, prenom, email, telephone, date_embauche, agence) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['telephone'], $_POST['date_embauche'], $_POST['agence']]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Suppression d'un agent
if (isset($_GET['supprimer'])) {
    $stmt = $pdo->prepare("DELETE FROM agents_bancaires WHERE id = ?");
    $stmt->execute([$_GET['supprimer']]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Récupération des agents
$query = $pdo->query("SELECT * FROM agents_bancaires");
$agents = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Agents Bancaires</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Gestion des Agents Bancaires</h2>
        <form method="POST" class="row g-3">
            <div class="col-md-2">
                <input type="text" name="nom" class="form-control" placeholder="Nom" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="prenom" class="form-control" placeholder="Prénom" required>
            </div>
            <div class="col-md-2">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="telephone" class="form-control" placeholder="Téléphone" required>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_embauche" class="form-control" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="agence" class="form-control" placeholder="Agence" required>
            </div>
            <div class="col-12 text-center">
                <button type="submit" name="ajouter_agent" class="btn btn-success">Ajouter Agent</button>
            </div>
        </form>
        
        <h3 class="mt-4">Liste des Agents</h3>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Date d'embauche</th>
                    <th>Agence</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($agents) > 0): ?>
                    <?php foreach ($agents as $agent): ?>
                        <tr>
                            <td><?= htmlspecialchars($agent['id']) ?></td>
                            <td><?= htmlspecialchars($agent['nom']) ?></td>
                            <td><?= htmlspecialchars($agent['prenom']) ?></td>
                            <td><?= htmlspecialchars($agent['email']) ?></td>
                            <td><?= htmlspecialchars($agent['telephone']) ?></td>
                            <td><?= htmlspecialchars($agent['date_embauche']) ?></td>
                            <td><?= htmlspecialchars($agent['agence']) ?></td>
                            <td>
                                <a href="?supprimer=<?= $agent['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet agent ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Aucun agent trouvé</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
