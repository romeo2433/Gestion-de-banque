<style>
.progress-wrapper {
    min-width: 160px;
}

.progress {
    background-color: #e9ecef;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

.progress-bar {
    transition: width 0.6s ease;
    border-radius: 50px;
}
</style>
<h2 class="text-primary mb-4">
    <i class="fas fa-calendar-alt me-2"></i>Gestion des Planifications
</h2>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>Planification supprimée avec succès !
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Info rôle -->
<div class="card mb-4">
    <div class="card-body">
        <i class="fas fa-user-shield me-2 text-primary"></i>
        Connecté en tant que : <strong><?= htmlspecialchars($_SESSION['role']) ?></strong>
        <?php if (isset($_SESSION['agence_id'])): ?>
            | <i class="fas fa-building me-1"></i>Agence: 
            <strong><?= htmlspecialchars($agence_nom ?? 'Non définie') ?></strong>
        <?php endif; ?>
    </div>
</div>

<!-- Recherche + Bouton Ajouter -->
<div class="row mb-4">
    <div class="col-md-8">
        <form method="GET" class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" name="search" class="form-control" 
                   value="<?= htmlspecialchars($search) ?>"
                   placeholder="Rechercher par titre ou description...">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-2"></i>Rechercher
            </button>
            <?php if (!empty($search)): ?>
                <a href="planifications.php" class="btn btn-secondary">Effacer</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="col-md-4 text-end">
        <a href="gestion_planification.php" class="btn btn-success">
            <i class="fas fa-plus-circle me-2"></i>Nouvelle Planification
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-list me-2"></i>Liste des planifications
        </div>
        <span class="badge bg-primary fs-6">
            Total : <?= count($planifications) ?> planification(s)
        </span>
    </div>
    
    <div class="card-body">
        <?php if (empty($planifications)): ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <p class="text-muted">Aucune planification trouvée.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Statut</th>
                            <th style="min-width: 140px;">Progression</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($planifications as $plan): ?>
                            <?php
                            $debut = new DateTime($plan['date_debut']);
                            $fin   = new DateTime($plan['date_fin']);
                            $now   = new DateTime();
                            
                            if ($now < $debut) {
                                $progression = 0;
                                $statut_label = 'À venir';
                            } elseif ($now > $fin) {
                                $progression = 100;
                                $statut_label = 'Terminé';
                            } else {
                                $total  = $debut->diff($fin)->days ?: 1;
                                $ecoule = $debut->diff($now)->days;
                                $progression = min(round(($ecoule / $total) * 100), 100);
                                $statut_label = 'En cours';
                            }
                            ?>
                            <tr>
                                <td><span class="badge bg-secondary">#<?= $plan['id'] ?></span></td>
                                <td><strong><?= htmlspecialchars($plan['titre']) ?></strong></td>
                                <td class="description-cell" title="<?= htmlspecialchars($plan['description'] ?? '') ?>">
                                    <?= htmlspecialchars($plan['description'] ?? '-') ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($plan['date_debut'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($plan['date_fin'])) ?></td>
                                <td>
                                    <?php 
                                    $badgeClass = match($plan['statut'] ?? '') {
                                        'en attente' => 'badge-attente',
                                        'en cours'   => 'badge-cours',
                                        'terminé'    => 'badge-termine',
                                        default      => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge-statut <?= $badgeClass ?>">
                                        <?= ucfirst(htmlspecialchars($plan['statut'] ?? 'Inconnu')) ?>
                                    </span>
                                </td>
                                <!-- Dans le <tbody>, remplace toute la colonne Progression par ceci : -->

                                <td>
                                    <?php
                                    $debut = new DateTime($plan['date_debut']);
                                    $fin   = new DateTime($plan['date_fin']);
                                    $now   = new DateTime();
                                    
                                    if ($now < $debut) {
                                        $progression = 0;
                                        $progress_color = 'bg-warning';
                                        $statut_label = 'À venir';
                                    } elseif ($now > $fin) {
                                        $progression = 100;
                                        $progress_color = 'bg-success';
                                        $statut_label = 'Terminé';
                                    } else {
                                        $total  = $debut->diff($fin)->days ?: 1;
                                        $ecoule = $debut->diff($now)->days;
                                        $progression = min(round(($ecoule / $total) * 100), 100);
                                        $progress_color = 'bg-info';
                                        $statut_label = 'En cours';
                                    }
                                    ?>
                                    
                                    <div class="progress-wrapper">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted"><?= $statut_label ?></small>
                                            <small class="fw-bold"><?= $progression ?>%</small>
                                        </div>
                                        <div class="progress" style="height: 10px; border-radius: 50px;">
                                            <div class="progress-bar <?= $progress_color ?>" 
                                                role="progressbar" 
                                                style="width: <?= $progression ?>%;" 
                                                aria-valuenow="<?= $progression ?>" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (peut_modifier($plan['id'])): ?>
                                        <a href="editplanification.php?id=<?= $plan['id'] ?>" 
                                           class="btn btn-warning btn-sm btn-action" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="deleteplanification.php?id=<?= $plan['id'] ?>" 
                                           class="btn btn-danger btn-sm btn-action"
                                           onclick="return confirm('Supprimer cette planification ?\n\n<?= addslashes($plan['titre']) ?>')"
                                           title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted"><i class="fas fa-ban"></i></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-4">
    <a href="dashboard.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour au Dashboard
    </a>
</div>