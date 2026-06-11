<h2 class="text-primary mb-4">
    ✏️ Modifier une demande de prêt
</h2>

<?php if (!empty($message)): ?>
    <div class="alert alert-success">
        <?= $message ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <?= $error ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">

        <form method="POST">

            <!-- CSRF -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="mb-3">
                <label class="form-label">Montant</label>
                <input type="number" step="0.01" name="montant"
                       class="form-control"
                       value="<?= htmlspecialchars($demande['montant']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Durée (mois)</label>
                <input type="number" name="duree"
                       class="form-control"
                       value="<?= htmlspecialchars($demande['duree']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Taux d'intérêt (%)</label>
                <input type="number" step="0.01" name="taux_interet"
                       class="form-control"
                       value="<?= htmlspecialchars($demande['taux_interet']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Type de prêt</label>
                <select name="type_pret" class="form-control" required>
                    <option value="consommation" <?= $demande['type_pret']=='consommation'?'selected':'' ?>>Consommation</option>
                    <option value="habitat" <?= $demande['type_pret']=='habitat'?'selected':'' ?>>Habitat</option>
                    <option value="auto" <?= $demande['type_pret']=='auto'?'selected':'' ?>>Auto</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Revenu</label>
                <input type="number" step="0.01" name="revenu"
                       class="form-control"
                       value="<?= htmlspecialchars($demande['revenu']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Motif</label>
                <textarea name="motif" rows="5"
                          class="form-control" required>
<?= htmlspecialchars($demande['motif']) ?>
                </textarea>
            </div>

            <button type="submit" class="btn btn-success">
                Enregistrer
            </button>

            <a href="espace_client.php" class="btn btn-secondary">
                Retour
            </a>

        </form>

    </div>
</div>