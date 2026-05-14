<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle demande de prêt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color:#f8f9fc;">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i>Nouvelle demande de prêt</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($_SESSION['erreurs'])): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach($_SESSION['erreurs'] as $err): ?>
                                    <li><?= htmlspecialchars($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['erreurs']); ?>
                    <?php endif; ?>
                    <form action="controller/traitement_pret.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Montant demandé (Ar)</label>
                                <input type="number" name="montant" class="form-control" min="100000" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Durée du prêt (mois)</label>
                                <select name="duree" class="form-select" required>
                                    <option value="">Choisir</option>
                                    <option value="6">6 mois</option>
                                    <option value="12">12 mois</option>
                                    <option value="24">24 mois</option>
                                    <option value="36">36 mois</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type de prêt</label>
                                <select name="type_pret" class="form-select" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="personnel">Personnel</option>
                                    <option value="immobilier">Immobilier</option>
                                    <option value="vehicule">Véhicule</option>
                                    <option value="etudiant">Étudiant</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Revenu mensuel (Ar)</label>
                                <input type="number" name="revenu" class="form-control" min="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Motif du prêt</label>
                            <textarea name="motif" rows="4" class="form-control" required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Document justificatif (PDF, JPG, PNG)</label>
                            <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="../espace_client.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Envoyer la demande
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>