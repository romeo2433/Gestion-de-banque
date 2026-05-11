<div class="main-content">
    
    <!-- Top Bar -->
    <div class="top-bar">
        <h1 class="page-title">
            <i class="fas fa-users"></i> Gestion des Agents Bancaires
        </h1>
        <div class="search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" id="searchAgent" placeholder="Rechercher un agent...">
        </div>
    </div>

    <!-- Messages d'alerte -->
    <?php if(isset($success_message) && $success_message): ?>
    <div class="alert-success-custom" style="
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05));
        border-left: 4px solid var(--success);
        padding: 16px 20px;
        border-radius: var(--radius-md);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    ">
        <i class="fas fa-check-circle" style="color: var(--success); font-size: 20px;"></i>
        <span style="color: var(--success); font-weight: 500;"><?= htmlspecialchars($success_message) ?></span>
        <button onclick="this.parentElement.style.display='none'" style="margin-left: auto; background: none; border: none; cursor: pointer;">
            <i class="fas fa-times" style="color: var(--gray);"></i>
        </button>
    </div>
    <?php endif; ?>

    <?php if(isset($error_message) && $error_message): ?>
    <div class="alert-danger-custom" style="
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
        border-left: 4px solid var(--danger);
        padding: 16px 20px;
        border-radius: var(--radius-md);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    ">
        <i class="fas fa-exclamation-triangle" style="color: var(--danger); font-size: 20px;"></i>
        <span style="color: var(--danger); font-weight: 500;"><?= htmlspecialchars($error_message) ?></span>
        <button onclick="this.parentElement.style.display='none'" style="margin-left: auto; background: none; border: none; cursor: pointer;">
            <i class="fas fa-times" style="color: var(--gray);"></i>
        </button>
    </div>
    <?php endif; ?>

    <!-- Formulaire d'ajout d'agent -->
    <div class="form-container" style="margin-bottom: 32px;">
        <h3 class="form-title">
            <i class="fas fa-user-plus"></i> Ajouter un nouvel agent
        </h3>
        
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">Nom</label>
                    <input type="text" name="nom" class="modern-input" placeholder="Dupont" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Prénom</label>
                    <input type="text" name="prenom" class="modern-input" placeholder="Jean" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Email</label>
                    <input type="email" name="email" class="modern-input" placeholder="jean.dupont@email.com" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="telephone" class="modern-input" placeholder="+33 6 12 34 56 78">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date d'embauche</label>
                    <input type="date" name="date_embauche" class="modern-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Agence</label>
                    <select name="agence" class="modern-select" required>
                        <option value="">Sélectionner une agence</option>
                        <?php foreach($agences as $a): ?>
                        <option value="<?= htmlspecialchars($a['nom']) ?>">
                            <?= htmlspecialchars($a['nom']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="btn-group" style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" name="ajouter" class="btn btn-success">
                    <i class="fas fa-save"></i> Ajouter l'agent
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-eraser"></i> Réinitialiser
                </button>
            </div>
        </form>
    </div>

    <!-- Tableau des agents -->
    <div class="table-wrapper">
        <div class="table-header">
            <h3 class="table-title">
                <i class="fas fa-list"></i> Liste des agents bancaires
            </h3>
            <div class="table-actions">
                <button class="btn btn-primary btn-sm" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt"></i> Actualiser
                </button>
                <button class="btn btn-outline btn-sm" id="exportBtn">
                    <i class="fas fa-download"></i> Exporter
                </button>
            </div>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="modern-table" id="agentsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Agence</th>
                        <th>Date d'embauche</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($agents) && !empty($agents)): ?>
                        <?php foreach($agents as $a): ?>
                        <tr>
                            <td>
                                <span class="badge" style="background: rgba(79, 70, 229, 0.1); color: var(--primary);">
                                    #<?= htmlspecialchars($a['id']) ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($a['nom'] . ' ' . $a['prenom']) ?></strong>
                            </td>
                            <td>
                                <i class="fas fa-envelope" style="color: var(--gray); font-size: 12px;"></i>
                                <?= htmlspecialchars($a['email']) ?>
                            </td>
                            <td>
                                <i class="fas fa-phone" style="color: var(--gray); font-size: 12px;"></i>
                                <?= htmlspecialchars($a['telephone'] ?? 'Non renseigné') ?>
                            </td>
                            <td>
                                <span class="badge" style="background: rgba(6, 182, 212, 0.1); color: var(--secondary);">
                                    <i class="fas fa-building"></i> <?= htmlspecialchars($a['agence']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if(isset($a['date_embauche']) && $a['date_embauche']): ?>
                                    <i class="fas fa-calendar-alt" style="color: var(--gray); font-size: 12px;"></i>
                                    <?= date('d/m/Y', strtotime($a['date_embauche'])) ?>
                                <?php else: ?>
                                    <span style="color: var(--gray);">Non définie</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn btn-sm btn-primary" onclick="editAgent(<?= $a['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $a['id'] ?>, '<?= htmlspecialchars($a['nom'] . ' ' . $a['prenom']) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 60px 20px;">
                                <i class="fas fa-users-slash" style="font-size: 48px; color: var(--gray); margin-bottom: 16px; display: block;"></i>
                                <p style="color: var(--gray);">Aucun agent trouvé</p>
                                <button class="btn btn-primary btn-sm" onclick="document.querySelector('.form-container').scrollIntoView({behavior: 'smooth'})">
                                    <i class="fas fa-plus"></i> Ajouter le premier agent
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination (si nécessaire) -->
        <?php if(isset($total_pages) && $total_pages > 1): ?>
        <div style="padding: 20px; border-top: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div style="color: var(--gray); font-size: 14px;">
                Affichage de <?= $debut ?? 1 ?> à <?= $fin ?? count($agents) ?> sur <?= $total_agents ?? count($agents) ?> agents
            </div>
            <div style="display: flex; gap: 8px;">
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <button class="btn btn-sm <?= ($page_actuelle ?? 1) == $i ? 'btn-primary' : 'btn-outline' ?>" 
                        onclick="window.location.href='?page=<?= $i ?>'">
                    <?= $i ?>
                </button>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- Modal de confirmation pour la suppression -->
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: var(--radius-xl); max-width: 400px; width: 90%; padding: 28px; box-shadow: var(--shadow-xl);">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 60px; height: 60px; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 28px; color: var(--danger);"></i>
            </div>
            <h3 style="margin-bottom: 8px;">Confirmer la suppression</h3>
            <p style="color: var(--gray);" id="deleteMessage">Êtes-vous sûr de vouloir supprimer cet agent ?</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <button class="btn btn-secondary" style="flex: 1;" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Annuler
            </button>
            <button class="btn btn-danger" style="flex: 1;" id="confirmDeleteBtn">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    </div>
</div>

<script>
    // Fonction de recherche
    document.getElementById('searchAgent')?.addEventListener('keyup', function() {
        let searchValue = this.value.toLowerCase();
        let table = document.getElementById('agentsTable');
        let rows = table.getElementsByTagName('tr');
        
        for(let i = 1; i < rows.length; i++) {
            let row = rows[i];
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        }
    });
    
    // Fonction de suppression avec confirmation
    let deleteId = null;
    
    function confirmDelete(id, name) {
        deleteId = id;
        document.getElementById('deleteMessage').innerHTML = `Êtes-vous sûr de vouloir supprimer l'agent <strong>${name}</strong> ?`;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        deleteId = null;
    }
    
    document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
        if(deleteId) {
            window.location.href = `?supprimer=${deleteId}`;
        }
    });
    
    // Fonction d'édition (à adapter selon vos besoins)
    function editAgent(id) {
        window.location.href = `?modifier=${id}`;
    }
    
    // Export CSV
    document.getElementById('exportBtn')?.addEventListener('click', function() {
        let table = document.getElementById('agentsTable');
        let rows = table.querySelectorAll('tr');
        let csv = [];
        
        for(let row of rows) {
            let cells = row.querySelectorAll('td, th');
            let rowData = [];
            for(let cell of cells) {
                rowData.push('"' + cell.textContent.replace(/"/g, '""') + '"');
            }
            csv.push(rowData.join(','));
        }
        
        let blob = new Blob([csv.join('\n')], {type: 'text/csv'});
        let url = URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = 'agents_bancaires.csv';
        a.click();
        URL.revokeObjectURL(url);
    });
    
    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if(e.target === this) {
            closeDeleteModal();
        }
    });
</script>

<style>
    /* Styles supplémentaires pour le modal */
    .btn-group {
        display: flex;
        gap: 12px;
    }
    
    @media (max-width: 768px) {
        .btn-group {
            flex-direction: column;
        }
    }
</style>