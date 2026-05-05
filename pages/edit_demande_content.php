<h2 class="mb-4">Modifier une Demande de Prêt</h2>

<form method="POST">

    <div class="mb-3">
        <label>Montant</label>
        <input type="number" name="montant" class="form-control"
               value="<?= htmlspecialchars($row['montant']) ?>" required>
    </div>

    <div class="mb-3">
        <label>Durée (mois)</label>
        <input type="number" name="duree" class="form-control"
               value="<?= htmlspecialchars($row['duree']) ?>" required>
    </div>

    <div class="mb-3">
        <label>Taux d'intérêt</label>
        <input type="number" step="0.01" name="taux_interet" class="form-control"
               value="<?= htmlspecialchars($row['taux_interet']) ?>" required>
    </div>

    <div class="mb-3">
        <label>Statut</label>
        <select name="statut" class="form-control">
            <option value="en attente" <?= $row['statut'] == 'en attente' ? 'selected' : '' ?>>En attente</option>
            <option value="approuvé" <?= $row['statut'] == 'approuvé' ? 'selected' : '' ?>>Approuvé</option>
            <option value="refusé" <?= $row['statut'] == 'refusé' ? 'selected' : '' ?>>Refusé</option>
        </select>
    </div>

    <button class="btn btn-success">Mettre à jour</button>
    <a href="demande.php" class="btn btn-secondary">Retour</a>

</form>