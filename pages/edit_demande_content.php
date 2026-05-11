<!-- Version adaptée avec le nouveau CSS -->
<div class="form-container">

    <h2 class="form-title">
        Modifier une Demande de Prêt
    </h2>

    <form method="POST" class="needs-validation">

        <div class="form-group">
            <label class="form-label required">Montant (€)</label>
            <input type="number"
                   name="montant"
                   class="modern-input"
                   value="<?= htmlspecialchars($row['montant']) ?>"
                   placeholder="Ex: 5000"
                   required>
        </div>

        <div class="form-grid">

            <div class="form-group">
                <label class="form-label required">Durée (mois)</label>
                <input type="number"
                       name="duree"
                       class="modern-input"
                       value="<?= htmlspecialchars($row['duree']) ?>"
                       placeholder="12"
                       required>
            </div>

            <div class="form-group">
                <label class="form-label required">Taux d'intérêt (%)</label>
                <input type="number"
                       step="0.01"
                       name="taux_interet"
                       class="modern-input"
                       value="<?= htmlspecialchars($row['taux_interet']) ?>"
                       placeholder="5.5"
                       required>
            </div>

        </div>

        <div class="form-group">
            <label class="form-label">Statut</label>
            <select name="statut" class="modern-select">
                <option value="en attente" <?= $row['statut'] == 'en attente' ? 'selected' : '' ?>>
                     En attente
                </option>
                <option value="approuvé" <?= $row['statut'] == 'approuvé' ? 'selected' : '' ?>>
                     Approuvé
                </option>
                <option value="refusé" <?= $row['statut'] == 'refusé' ? 'selected' : '' ?>>
                     Refusé
                </option>
            </select>
        </div>

        <!-- Groupe de boutons -->
        <div class="btn-group" style="display: flex; gap: 12px; margin-top: 32px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i>
                Mettre à jour
            </button>

            <a href="demande.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
        </div>

    </form>

</div>