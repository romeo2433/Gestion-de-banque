<h2 class="text-primary mb-4">
    <i class="fas fa-hand-holding-usd me-2"></i>
    Création de prêt (Admin)
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

            <div class="mb-3">
                <label class="form-label">Client</label>
                <select name="utilisateur_id" class="form-control" required>

                    <option value="">
                        Sélectionner un client
                    </option>

                    <?php foreach($users as $u): ?>
                        <option value="<?= $u['id'] ?>">
                            <?= htmlspecialchars($u['username']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Montant</label>
                <input type="number"
                       step="0.01"
                       name="montant"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Durée (mois)</label>
                <input type="number"
                       name="duree"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Taux d'intérêt (%)</label>
                <input type="number"
                       step="0.1"
                       name="taux_interet"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Type de prêt</label>
                <select name="type_pret" class="form-select" required>
                    <option value="">Sélectionner un type</option>
                    <option value="personnel">Personnel</option>
                    <option value="immobilier">Immobilier</option>
                    <option value="vehicule">Véhicule</option>
                    <option value="etudiant">Étudiant</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Revenu</label>
                <input type="number"
                       step="0.01"
                       name="revenu"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Motif</label>
                <textarea name="motif"
                          class="form-control"
                          rows="4"
                          required></textarea>
            </div>

            <button type="submit" class="btn btn-success">
                Créer le prêt
            </button>

            <a href="demande.php"
               class="btn btn-secondary">
                Retour
            </a>

        </form>

    </div>
</div>