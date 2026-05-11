<!-- ============================================ -->
<!-- dashboard_content.php - Dashboard Principal  -->
<!-- Utilise UNIQUEMENT les classes de style.css -->
<!-- ============================================ -->

<div class="main-content">
    
    <!-- Top Bar -->
    <div class="top-bar">
        <h1 class="page-title">
            <i class="fas fa-chart-line"></i> Dashboard
        </h1>
        <div class="search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" id="dashboardSearch" placeholder="Rechercher...">
        </div>
    </div>

    <!-- Affichage des erreurs -->
    <?php if (isset($error) && $error): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <!-- Cartes Statistiques -->
    <div class="stats-grid">
        
        <!-- Carte Clients -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-chart-line"></i> +<?php echo $taux_croissance_clients ?? '0'; ?>%
                </div>
            </div>
            <div class="stat-value"><?php echo number_format($total_clients ?? 0); ?></div>
            <div class="stat-label">Clients</div>
            <div class="stat-footer">
                <i class="fas fa-user-plus"></i> 
                <?php echo $nouveaux_clients_mois ?? '0'; ?> nouveaux ce mois
            </div>
        </div>

        <!-- Carte Prêts -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="stat-trend success">
                    <i class="fas fa-arrow-up"></i> Actifs
                </div>
            </div>
            <div class="stat-value"><?php echo number_format($total_prets ?? 0); ?></div>
            <div class="stat-label">Prêts</div>
            <div class="stat-footer">
                <i class="fas fa-check-circle"></i> 
                <?php echo $prets_actifs ?? '0'; ?> prêts actifs
            </div>
        </div>

        <!-- Carte Montant Total -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-trend warning">
                    <i class="fas fa-chart-bar"></i> Total
                </div>
            </div>
            <div class="stat-value"><?php echo number_format($montant_total_prets ?? 0); ?> Ar</div>
            <div class="stat-label">Montant total des prêts</div>
            <div class="stat-footer">
                <i class="fas fa-chart-line"></i> 
                Moyenne: <?php echo number_format($montant_moyen ?? 0); ?> Ar
            </div>
        </div>

        <!-- Carte Taux de remboursement -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-percent"></i>
                </div>
                <div class="stat-trend purple">
                    <i class="fas fa-chart-pie"></i> Performance
                </div>
            </div>
            <div class="stat-value"><?php echo $taux_remboursement ?? '94'; ?>%</div>
            <div class="stat-label">Taux de remboursement</div>
            <div class="progress-bar-custom">
                <div class="progress-fill" style="width: <?php echo $taux_remboursement ?? '94'; ?>%"></div>
            </div>
        </div>

    </div>

    <!-- Section des dernières activités -->
    <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
        
        <!-- Derniers prêts -->
        <div class="table-wrapper">
            <div class="table-header">
                <h3 class="table-title">
                    <i class="fas fa-history"></i> Derniers prêts
                </h3>
                <a href="demande.php" class="btn btn-outline btn-sm">Voir tout</a>
            </div>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($derniers_prets) && !empty($derniers_prets)): ?>
                        <?php foreach($derniers_prets as $pret): ?>
                        <tr>
                            <td><?= htmlspecialchars($pret['client_nom'] ?? $pret['nom'] ?? 'Client') ?></td
                            <td><?= number_format($pret['montant'] ?? 0) ?> Ar</td
                            <td>
                                <?php 
                                $statut = $pret['statut'] ?? 'en attente';
                                $badgeClass = match($statut) {
                                    'approuvé' => 'badge-success',
                                    'refusé' => 'badge-danger',
                                    default => 'badge-warning'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst($statut) ?>
                                </span>
                             </td
                         </tr
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 40px;">
                                <i class="fas fa-inbox"></i> Aucun prêt récent
                            </td>
                         </tr
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Distribution des prêts -->
        <div class="table-wrapper">
            <div class="table-header">
                <h3 class="table-title">
                    <i class="fas fa-chart-pie"></i> Distribution
                </h3>
            </div>
            <div style="padding: 20px;">
                <div class="stat-item">
                    <div class="stat-item-label">
                        <span class="badge badge-success">✅ Approuvés</span>
                        <span><?= $pourcentage_approuves ?? '65' ?>%</span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill success" style="width: <?= $pourcentage_approuves ?? '65' ?>%"></div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-item-label">
                        <span class="badge badge-warning">⏳ En attente</span>
                        <span><?= $pourcentage_attente ?? '25' ?>%</span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill warning" style="width: <?= $pourcentage_attente ?? '25' ?>%"></div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-item-label">
                        <span class="badge badge-danger">❌ Refusés</span>
                        <span><?= $pourcentage_refuses ?? '10' ?>%</span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill danger" style="width: <?= $pourcentage_refuses ?? '10' ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<style>
/* Styles supplémentaires pour le dashboard (si besoin) */
.stat-footer {
    margin-top: 12px;
    font-size: 12px;
    color: var(--gray);
}

.stat-trend.success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.stat-trend.warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.stat-trend.purple {
    background: rgba(139, 92, 246, 0.1);
    color: #8b5cf6;
}

.progress-bar-custom {
    width: 100%;
    height: 6px;
    background: var(--gray-light);
    border-radius: 3px;
    overflow: hidden;
    margin-top: 12px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    border-radius: 3px;
    transition: width 1s ease;
}

.progress-fill.success {
    background: linear-gradient(135deg, var(--success), #059669);
}

.progress-fill.warning {
    background: linear-gradient(135deg, var(--warning), #d97706);
}

.progress-fill.danger {
    background: linear-gradient(135deg, var(--danger), #dc2626);
}

.stat-item {
    margin-bottom: 20px;
}

.stat-item-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 14px;
}

.alert {
    padding: 16px 20px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-danger {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
    border-left: 4px solid var(--danger);
    color: var(--danger);
    font-weight: 500;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
    // Recherche dans le dashboard
    document.getElementById('dashboardSearch')?.addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            let text = card.textContent.toLowerCase();
            card.style.display = text.includes(value) ? '' : 'none';
        });
    });
</script>