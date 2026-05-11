<!-- ============================================ -->
<!-- historique_content.php - Vue Historique      -->
<!-- Structure identique à votre demande          -->
<!-- ============================================ -->

<div class="main-content">
    
    <!-- Top Bar -->
    <div class="top-bar">
        <h1 class="page-title">
            <i class="fas fa-history"></i> Historique des opérations
        </h1>
        <div class="search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" id="globalSearch" placeholder="Rechercher...">
        </div>
    </div>

    <!-- Messages d'alerte -->
    <?php if(isset($error) && $error): ?>
    <div class="alert-danger-custom" style="
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
        border-left: 4px solid #ef4444;
        padding: 16px 20px;
        border-radius: 16px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    ">
        <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 20px;"></i>
        <span style="color: #dc2626; font-weight: 500;"><?= htmlspecialchars($error) ?></span>
        <button onclick="this.parentElement.style.display='none'" style="margin-left: auto; background: none; border: none; cursor: pointer;">
            <i class="fas fa-times" style="color: #94a3b8;"></i>
        </button>
    </div>
    <?php endif; ?>

    <!-- Info rôle (comme votre formulaire d'agent) -->
    <div class="info-role" style="
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(6, 182, 212, 0.05));
        padding: 16px 20px;
        border-radius: 16px;
        margin-bottom: 24px;
        border-left: 4px solid #4f46e5;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    ">
        <i class="fas fa-user-shield" style="color: #4f46e5;"></i>
        <span>Connecté en tant que : <strong><?= htmlspecialchars($role ?? $_SESSION['role'] ?? 'agent') ?></strong></span>
        <?php if(isset($agence_nom) && $agence_nom): ?>
        <span>| <i class="fas fa-building"></i> Agence : <strong><?= htmlspecialchars($agence_nom) ?></strong></span>
        <?php endif; ?>
        <span style="margin-left: auto;">
            <i class="fas fa-calendar-alt"></i> <?= date('d/m/Y H:i') ?>
        </span>
    </div>

    <!-- Section Filtres (comme votre formulaire d'ajout d'agent) -->
    <div class="form-container" style="margin-bottom: 32px;">
        <h3 class="form-title">
            <i class="fas fa-filter"></i> Filtres de recherche
        </h3>
        
        <form method="GET" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <select name="status" class="modern-select">
                        <option value="">Tous les statuts</option>
                        <option value="approuvé" <?= ($statusFilter ?? '') == 'approuvé' ? 'selected' : '' ?>>Approuvé</option>
                        <option value="en attente" <?= ($statusFilter ?? '') == 'en attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="refusé" <?= ($statusFilter ?? '') == 'refusé' ? 'selected' : '' ?>>Refusé</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date début</label>
                    <input type="date" name="dateDebut" class="modern-input" value="<?= htmlspecialchars($dateDebut ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date fin</label>
                    <input type="date" name="dateFin" class="modern-input" value="<?= htmlspecialchars($dateFin ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="type" class="modern-select">
                        <option value="all" <?= ($_GET['type'] ?? 'all') == 'all' ? 'selected' : '' ?>>Tous</option>
                        <option value="prets" <?= ($_GET['type'] ?? 'all') == 'prets' ? 'selected' : '' ?>>Prêts</option>
                        <option value="remboursements" <?= ($_GET['type'] ?? 'all') == 'remboursements' ? 'selected' : '' ?>>Remboursements</option>
                        <option value="clients" <?= ($_GET['type'] ?? 'all') == 'clients' ? 'selected' : '' ?>>Clients</option>
                    </select>
                </div>
            </div>
            
            <div class="btn-group" style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrer
                </button>
                <a href="historique.php" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Section 1: Demandes de prêt -->
    <div class="table-wrapper" style="margin-bottom: 32px;">
        <div class="table-header">
            <h3 class="table-title">
                <i class="fas fa-hand-holding-usd"></i> Historique des demandes de prêt
            </h3>
            <div class="table-actions">
                <span class="stats-badge bg-primary">
                    <i class="fas fa-chart-bar"></i> Total: <?= $statsPrets['total'] ?? 0 ?>
                </span>
                <span class="stats-badge bg-success">
                    <i class="fas fa-check-circle"></i> <?= $statsPrets['approuves'] ?? 0 ?> approuvés
                </span>
                <span class="stats-badge bg-warning">
                    <i class="fas fa-clock"></i> <?= $statsPrets['en_attente'] ?? 0 ?> en attente
                </span>
                <span class="stats-badge bg-danger">
                    <i class="fas fa-times-circle"></i> <?= $statsPrets['refuses'] ?? 0 ?> refusés
                </span>
            </div>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="modern-table" id="pretTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Montant</th>
                        <th>Durée</th>
                        <th>Taux</th>
                        <th>Statut</th>
                        <th>Date demande</th>
                        <th>Agence</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($prets) && !empty($prets)): ?>
                        <?php foreach($prets as $pret): ?>
                        <tr>
                            <td>
                                <span class="badge" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                                    #<?= htmlspecialchars($pret['id']) ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($pret['nom'] . ' ' . $pret['prenom']) ?></strong>
                            </td>
                            <td style="text-align: right;"><?= number_format($pret['montant'], 0, ',', ' ') ?> Ar</td>
                            <td><?= $pret['duree'] ?> mois</td>
                            <td><?= $pret['taux_interet'] ?>%</td>
                            <td>
                                <?php
                                $badgeClass = '';
                                switch($pret['statut']) {
                                    case 'approuvé': $badgeClass = 'success'; break;
                                    case 'en attente': $badgeClass = 'warning'; break;
                                    case 'refusé': $badgeClass = 'danger'; break;
                                    default: $badgeClass = 'secondary';
                                }
                                ?>
                                <span class="badge badge-<?= $badgeClass ?>">
                                    <?= ucfirst($pret['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <i class="fas fa-calendar-alt" style="color: #94a3b8; font-size: 12px;"></i>
                                <?= date('d/m/Y', strtotime($pret['date_demande'])) ?>
                            </td>
                            <td>
                                <?php if(!empty($pret['agence_nom'])): ?>
                                <span class="badge" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                                    <i class="fas fa-building"></i> <?= htmlspecialchars($pret['agence_nom']) ?>
                                </span>
                                <?php else: ?>
                                <span style="color: #94a3b8;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn btn-sm btn-primary" onclick="showDetails('pret', <?= $pret['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="printDetails('pret', <?= $pret['id'] ?>)">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 60px 20px;">
                                <i class="fas fa-inbox" style="font-size: 48px; color: #94a3b8; margin-bottom: 16px; display: block;"></i>
                                <p style="color: #94a3b8;">Aucune demande de prêt trouvée</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Remboursements -->
    <div class="table-wrapper" style="margin-bottom: 32px;">
        <div class="table-header">
            <h3 class="table-title">
                <i class="fas fa-money-bill-wave"></i> Historique des remboursements
            </h3>
            <div class="table-actions">
                <span class="stats-badge bg-primary">
                    <i class="fas fa-chart-bar"></i> Total: <?= $statsPaiements['total'] ?? 0 ?>
                </span>
                <span class="stats-badge bg-success">
                    <i class="fas fa-check-circle"></i> <?= $statsPaiements['rembourses'] ?? 0 ?> remboursés
                </span>
                <span class="stats-badge bg-warning">
                    <i class="fas fa-clock"></i> <?= $statsPaiements['en_attente'] ?? 0 ?> en attente
                </span>
                <span class="stats-badge bg-danger">
                    <i class="fas fa-times-circle"></i> <?= $statsPaiements['annules'] ?? 0 ?> annulés
                </span>
            </div>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="modern-table" id="paiementsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Montant</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Agence</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($paiements) && !empty($paiements)): ?>
                        <?php foreach($paiements as $paiement): ?>
                        <tr>
                            <td>
                                <span class="badge" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                                    #<?= htmlspecialchars($paiement['id']) ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($paiement['nom'] . ' ' . $paiement['prenom']) ?></strong>
                            </td>
                            <td style="text-align: right;"><?= number_format($paiement['montant'], 0, ',', ' ') ?> Ar</td>
                            <td>
                                <i class="fas fa-calendar-alt" style="color: #94a3b8; font-size: 12px;"></i>
                                <?= date('d/m/Y', strtotime($paiement['date_remboursement'])) ?>
                            </td>
                            <td>
                                <?php
                                $badgeClass = '';
                                switch($paiement['statut']) {
                                    case 'remboursé': $badgeClass = 'success'; break;
                                    case 'en attente': $badgeClass = 'warning'; break;
                                    case 'annulé': $badgeClass = 'danger'; break;
                                    default: $badgeClass = 'secondary';
                                }
                                ?>
                                <span class="badge badge-<?= $badgeClass ?>">
                                    <?= ucfirst($paiement['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if(!empty($paiement['agence_nom'])): ?>
                                <span class="badge" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                                    <i class="fas fa-building"></i> <?= htmlspecialchars($paiement['agence_nom']) ?>
                                </span>
                                <?php else: ?>
                                <span style="color: #94a3b8;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn btn-sm btn-primary" onclick="showDetails('paiement', <?= $paiement['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="printDetails('paiement', <?= $paiement['id'] ?>)">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 60px 20px;">
                                <i class="fas fa-inbox" style="font-size: 48px; color: #94a3b8; margin-bottom: 16px; display: block;"></i>
                                <p style="color: #94a3b8;">Aucun remboursement trouvé</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 3: Clients -->
    <div class="table-wrapper">
        <div class="table-header">
            <h3 class="table-title">
                <i class="fas fa-users"></i> Liste des clients
            </h3>
            <div class="table-actions">
                <span class="stats-badge bg-primary">
                    <i class="fas fa-users"></i> <?= $statsClients['total'] ?? 0 ?> clients
                </span>
                <span class="stats-badge bg-success">
                    <i class="fas fa-check-circle"></i> <?= $statsClients['actifs'] ?? 0 ?> actifs
                </span>
                <span class="stats-badge bg-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?= $statsClients['retard'] ?? 0 ?> en retard
                </span>
            </div>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="modern-table" id="clientsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Prêts</th>
                        <th>Montant total</th>
                        <th>Remboursé</th>
                        <th>Restant</th>
                        <th>Statut</th>
                        <th>Agence</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($clients) && !empty($clients)): ?>
                        <?php foreach($clients as $client): 
                            $reste = ($client['montant_total_prets'] ?? 0) - ($client['total_rembourse'] ?? 0);
                        ?>
                        <tr>
                            <td>
                                <span class="badge" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                                    #<?= htmlspecialchars($client['id']) ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($client['nom'] . ' ' . $client['prenom']) ?></strong>
                            </td>
                            <td>
                                <i class="fas fa-envelope" style="color: #94a3b8; font-size: 12px;"></i>
                                <?= htmlspecialchars($client['email']) ?>
                            </td>
                            <td>
                                <?php if(!empty($client['telephone'])): ?>
                                <i class="fas fa-phone" style="color: #94a3b8; font-size: 12px;"></i>
                                <?= htmlspecialchars($client['telephone']) ?>
                                <?php else: ?>
                                <span style="color: #94a3b8;">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $client['nb_prets'] ?? 0 ?></td>
                            <td style="text-align: right;"><?= number_format($client['montant_total_prets'] ?? 0, 0, ',', ' ') ?> Ar</td>
                            <td style="text-align: right; color: #10b981;"><?= number_format($client['total_rembourse'] ?? 0, 0, ',', ' ') ?> Ar</td>
                            <td style="text-align: right; color: #ef4444;"><?= number_format($reste, 0, ',', ' ') ?> Ar</td>
                            <td>
                                <?php
                                $badgeClass = '';
                                switch($client['statut_global'] ?? 'Inactif') {
                                    case 'Actif': $badgeClass = 'success'; break;
                                    case 'En retard': $badgeClass = 'danger'; break;
                                    default: $badgeClass = 'secondary';
                                }
                                ?>
                                <span class="badge badge-<?= $badgeClass ?>">
                                    <?= $client['statut_global'] ?? 'Inactif' ?>
                                </span>
                            </td>
                            <td>
                                <?php if(!empty($client['agence_nom'])): ?>
                                <span class="badge" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                                    <i class="fas fa-building"></i> <?= htmlspecialchars($client['agence_nom']) ?>
                                </span>
                                <?php else: ?>
                                <span style="color: #94a3b8;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn btn-sm btn-primary" onclick="showDetails('client', <?= $client['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="printDetails('client', <?= $client['id'] ?>)">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 60px 20px;">
                                <i class="fas fa-users-slash" style="font-size: 48px; color: #94a3b8; margin-bottom: 16px; display: block;"></i>
                                <p style="color: #94a3b8;">Aucun client trouvé</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal de détails -->
<div id="detailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 28px; max-width: 600px; width: 90%; max-height: 85vh; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="padding: 20px 24px; background: linear-gradient(135deg, #4f46e5, #4338ca); color: white; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;" id="modalTitle">
                <i class="fas fa-info-circle"></i> Détails
            </h3>
            <button onclick="closeModal()" style="background: none; border: none; color: white; font-size: 28px; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 24px; overflow-y: auto; max-height: 60vh;" id="modalBody">
            <div style="text-align: center; padding: 40px;">
                <div style="width: 40px; height: 40px; border: 3px solid #e2e8f0; border-top-color: #4f46e5; border-radius: 50%; animation: spin 0.6s linear infinite; margin: 0 auto 16px;"></div>
                <p>Chargement...</p>
            </div>
        </div>
        <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 12px;">
            <button class="btn btn-secondary" onclick="closeModal()">
                <i class="fas fa-times"></i> Fermer
            </button>
            <button class="btn btn-primary" onclick="printModal()">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>
</div>

<style>
    /* Animation */
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Badges */
    .badge-success {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-warning {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-secondary {
        background: rgba(100, 116, 139, 0.15);
        color: #64748b;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .stats-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
        margin-right: 8px;
        margin-bottom: 8px;
    }
    
    .stats-badge i {
        margin-right: 6px;
    }
    
    .stats-badge.bg-primary {
        background: linear-gradient(135deg, #4f46e5, #4338ca);
        color: white;
    }
    
    .stats-badge.bg-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .stats-badge.bg-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }
    
    .stats-badge.bg-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    
    .action-btns {
        display: flex;
        gap: 8px;
    }
    
    .btn-group {
        display: flex;
        gap: 12px;
    }
    
    @media (max-width: 768px) {
        .btn-group {
            flex-direction: column;
        }
        .action-btns {
            flex-direction: column;
        }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    // Initialisation DataTables
    $(document).ready(function() {
        if ($('#pretTable tbody tr').length > 1 || ($('#pretTable tbody tr').length == 1 && $('#pretTable tbody tr td').attr('colspan') != 9)) {
            $('#pretTable').DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json" },
                pageLength: 10,
                responsive: true
            });
        }
        
        if ($('#paiementsTable tbody tr').length > 1 || ($('#paiementsTable tbody tr').length == 1 && $('#paiementsTable tbody tr td').attr('colspan') != 7)) {
            $('#paiementsTable').DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json" },
                pageLength: 10,
                responsive: true
            });
        }
        
        if ($('#clientsTable tbody tr').length > 1 || ($('#clientsTable tbody tr').length == 1 && $('#clientsTable tbody tr td').attr('colspan') != 11)) {
            $('#clientsTable').DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json" },
                pageLength: 10,
                responsive: true
            });
        }
    });
    
    // Recherche globale
    document.getElementById('globalSearch')?.addEventListener('keyup', function() {
        let searchValue = this.value.toLowerCase();
        let tables = ['pretTable', 'paiementsTable', 'clientsTable'];
        
        tables.forEach(tableId => {
            let table = document.getElementById(tableId);
            if (table) {
                let rows = table.getElementsByTagName('tr');
                for(let i = 1; i < rows.length; i++) {
                    let row = rows[i];
                    let text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchValue) ? '' : 'none';
                }
            }
        });
    });
    
    // Fonctions modal
    function showDetails(type, id) {
        document.getElementById('modalBody').innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <div style="width: 40px; height: 40px; border: 3px solid #e2e8f0; border-top-color: #4f46e5; border-radius: 50%; animation: spin 0.6s linear infinite; margin: 0 auto 16px;"></div>
                <p>Chargement des détails...</p>
            </div>
        `;
        document.getElementById('detailsModal').style.display = 'flex';
        
        $.ajax({
            url: 'get_details.php',
            type: 'GET',
            data: { type: type, id: id },
            dataType: 'json',
            success: function(response) {
                document.getElementById('modalTitle').innerHTML = `<i class="fas fa-info-circle"></i> ${response.title || 'Détails'}`;
                document.getElementById('modalBody').innerHTML = response.content || '<p>Aucune information disponible</p>';
            },
            error: function() {
                document.getElementById('modalBody').innerHTML = '<p style="color: #ef4444;">Erreur lors du chargement des détails</p>';
            }
        });
    }
    
    function closeModal() {
        document.getElementById('detailsModal').style.display = 'none';
    }
    
    function printDetails(type, id) {
        window.open(`print_details.php?type=${type}&id=${id}`, '_blank');
    }
    
    function printModal() {
        const content = document.getElementById('modalBody').innerHTML;
        const title = document.getElementById('modalTitle').innerText;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head><title>${title}</title></head>
                <body style="font-family: Arial, sans-serif; padding: 20px;">
                    <h2 style="color: #4f46e5;">${title}</h2>
                    <hr>
                    ${content}
                    <hr>
                    <p style="text-align: center; font-size: 12px; color: #666;">Imprimé le ${new Date().toLocaleString()}</p>
                </body>
            </html>
        `);
        printWindow.print();
        printWindow.close();
    }
    
    // Fermer modal en cliquant à l'extérieur
    document.getElementById('detailsModal')?.addEventListener('click', function(e) {
        if(e.target === this) {
            closeModal();
        }
    });
</script>