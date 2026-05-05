<h2 class="text-primary mb-4">
    <i class="fas fa-edit me-2"></i>Modifier la Planification
</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($planification): ?>
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <i class="fas fa-calendar-alt me-2"></i>
            Modification - #<?= $planification['id'] ?>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Titre <span class="text-danger">*</span></label>
                        <input type="text" name="titre" class="form-control" 
                               value="<?= htmlspecialchars($planification['titre']) ?>" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($planification['description'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Date de Début <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="date_debut" class="form-control" 
                               value="<?= date('Y-m-d\TH:i', strtotime($planification['date_debut'])) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Date de Fin <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="date_fin" class="form-control" 
                               value="<?= date('Y-m-d\TH:i', strtotime($planification['date_fin'])) ?>" required>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-bold">Statut</label>
                        <select name="statut" class="form-select" required>
                            <option value="en attente" <?= $planification['statut'] == 'en attente' ? 'selected' : '' ?>>En Attente</option>
                            <option value="en cours"   <?= $planification['statut'] == 'en cours'   ? 'selected' : '' ?>>En Cours</option>
                            <option value="terminé"    <?= $planification['statut'] == 'terminé'    ? 'selected' : '' ?>>Terminé</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
                    </button>
                    <a href="planifications.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>