<?php
// Activation des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrer la session
session_start();

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $utilisateur_id = filter_input(INPUT_POST, 'utilisateur_id', FILTER_VALIDATE_INT);
        $montant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);
        $date_remboursement = filter_input(INPUT_POST, 'date_remboursement', FILTER_SANITIZE_STRING);
        $statut = filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_STRING);
        
        // Vérification des données obligatoires
        if (!$utilisateur_id || $montant === false || !$date_remboursement || !$statut) {
            throw new Exception("Données invalides");
        }

        $sql = "INSERT INTO remboursement (utilisateur_id, montant, date_remboursement, statut) 
                VALUES (:utilisateur_id, :montant, :date_remboursement, :statut)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'utilisateur_id' => $utilisateur_id,
            'montant' => $montant,
            'date_remboursement' => $date_remboursement,
            'statut' => $statut
        ]);

        $_SESSION['message'] = "Remboursement ajouté avec succès";
        header("Location: remboursement.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
    }
}

$sql = "SELECT id, nom, prenom FROM users ORDER BY nom, prenom";
$stmt = $conn->prepare($sql);
$stmt->execute();
$utilisateurs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Remboursement</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .container { max-width: 600px; margin-top: 50px; }
    </style>
</head>
<body class="container">
    <div class="card shadow-sm mt-5">
        <div class="card-header bg-success text-white">
            <h2 class="mb-0">Ajouter un Remboursement</h2>
        </div>
        <div class="card-body">
            <?php 
            // Affichage des messages d'erreur
            if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" novalidate>
                <div class="mb-3">
                    <label class="form-label">Utilisateur :</label>
                    <select name="utilisateur_id" class="form-select" required>
                        <option value="">Sélectionner un utilisateur</option>
                        <?php foreach ($utilisateurs as $utilisateur): ?>
                            <option value="<?= $utilisateur['id'] ?>"><?= htmlspecialchars($utilisateur['nom'] . ' ' . $utilisateur['prenom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Montant :</label>
                    <input type="number" step="0.01" name="montant" class="form-control" required min="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Date de Remboursement :</label>
                    <input type="datetime-local" name="date_remboursement" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Statut :</label>
                    <select name="statut" class="form-select" required>
                        <option value="en attente">En attente</option>
                        <option value="remboursé">Remboursé</option>
                        <option value="annulé">Annulé</option>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Ajouter</button>
                    <a href="remboursement.php" class="btn btn-secondary">Retour</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>