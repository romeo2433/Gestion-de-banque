<!-- ============================================ -->
<!-- clients_content.php - Gestion des Clients    -->
<!-- Utilise UNIQUEMENT les classes de style.css -->
<!-- AUCUN style supplémentaire                  -->
<!-- ============================================ -->

<div class="main-content">
    
    <!-- Top Bar -->
    <div class="top-bar">
        <h1 class="page-title">
            <i class="fas fa-users"></i> Gestion des Clients
        </h1>
        <div class="search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" id="globalSearch" placeholder="Rechercher un client...">
        </div>
    </div>

    <!-- Messages d'alerte -->
    <?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Opération réussie !
    </div>
    <?php endif; ?>

    <?php if(isset($error) && $error): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Formulaire de recherche -->
    <div class="form-container">
        <h3 class="form-title">
            <i class="fas fa-search"></i> Rechercher un client
        </h3>
        
        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="search" class="modern-input" 
                       value="<?= htmlspecialchars($search ?? '') ?>" 
                       placeholder="Nom, email ou téléphone...">
            </div>
            <div class="btn-group" style="margin-top: 16px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Rechercher
                </button>
                <a href="create.php" class="btn btn-success">
                    <i class="fas fa-user-plus"></i> Ajouter
                </a>
            </div>
        </form>
    </div>

   <!-- ========================================= -->
<!-- TABLEAU DES CLIENTS -->
<!-- ========================================= -->

<div class="table-wrapper">

<!-- HEADER -->
<div class="table-header">

    <h3 class="table-title">
        <i class="fas fa-users"></i>
        Liste des clients

        <span class="stats-badge bg-primary" style="margin-left:12px;">
            <i class="fas fa-chart-bar"></i>
            Total : <?= count($utilisateurs ?? []) ?>
        </span>
    </h3>

    <div class="table-actions">

        <button class="btn btn-secondary btn-sm" id="exportBtn">
            <i class="fas fa-download"></i>
            Exporter
        </button>

        <a href="create.php" class="btn btn-primary btn-sm">
            <i class="fas fa-user-plus"></i>
            Ajouter
        </a>

    </div>

</div>

<!-- TABLE -->
<div class="table-responsive">

    <table class="modern-table" id="clientsTable">

        <thead>
            <tr>
                <th>ID</th>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Agence</th>
                <th style="text-align:center;">Actions</th>
            </tr>
        </thead>

        <tbody>

            <?php if(isset($utilisateurs) && !empty($utilisateurs)): ?>

                <?php foreach($utilisateurs as $user): ?>

                    <tr>

                        <!-- ID -->
                        <td>
                            <span class="badge-id">
                                #<?= $user['id'] ?>
                            </span>
                        </td>

                        <!-- NOM -->
                        <td>

                            <div class="user-info-table">

                                <div class="user-avatar-sm">
                                    <?= strtoupper(substr($user['nom'],0,1)) ?>
                                </div>

                                <div>
                                    <strong>
                                        <?= htmlspecialchars($user['nom'] . ' ' . ($user['prenom'] ?? '')) ?>
                                    </strong>
                                </div>

                            </div>

                        </td>

                        <!-- EMAIL -->
                        <td>

                            <a href="mailto:<?= htmlspecialchars($user['email']) ?>"
                               class="table-link">

                                <i class="fas fa-envelope"></i>

                                <?= htmlspecialchars($user['email']) ?>

                            </a>

                        </td>

                        <!-- AGENCE -->
                        <td>

                            <?php if(!empty($user['agence_nom'])): ?>

                                <span class="stats-badge bg-success">

                                    <i class="fas fa-building"></i>

                                    <?= htmlspecialchars($user['agence_nom']) ?>

                                </span>

                            <?php else: ?>

                                <span class="stats-badge bg-danger">
                                    Non assigné
                                </span>

                            <?php endif; ?>

                        </td>

                        <!-- ACTIONS -->
                        <td>

                            <div class="action-buttons">

                                <!-- MODIFIER -->
                                <a href="edit.php?id=<?= $user['id'] ?>"
                                   class="btn btn-primary btn-sm"
                                   title="Modifier">

                                    <i class="fas fa-edit"></i>

                                </a>

                                <!-- VOIR 
                                <a href="view.php?id=<?= $user['id'] ?>"
                                   class="btn btn-secondary btn-sm"
                                   title="Voir">

                                    <i class="fas fa-eye"></i>

                                </a>-->

                                <!-- SUPPRIMER -->
                                <button class="btn btn-danger btn-sm"
                                        title="Supprimer"
                                        onclick="confirmDelete(
                                            <?= $user['id'] ?>,
                                            '<?= htmlspecialchars($user['nom']) ?>'
                                        )">

                                    <i class="fas fa-trash"></i>

                                </button>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="5" class="empty-state">

                        <i class="fas fa-users-slash empty-icon"></i>

                        <h3>Aucun client trouvé</h3>

                        <p>
                            Aucun utilisateur n'est enregistré
                            pour le moment.
                        </p>

                        <a href="create.php"
                           class="btn btn-primary">

                            <i class="fas fa-plus"></i>

                            Ajouter un client

                        </a>

                    </td>

                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>

</div>y

</div>

<!-- Modal de confirmation -->
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: var(--radius-xl); max-width: 400px; width: 90%; padding: 28px; text-align: center;">
        <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: var(--danger); margin-bottom: 16px;"></i>
        <h3>Confirmer la suppression</h3>
        <p style="color: var(--gray); margin: 16px 0;" id="deleteMessage">Êtes-vous sûr ?</p>
        <div class="btn-group" style="justify-content: center;">
            <button class="btn btn-secondary" onclick="closeDeleteModal()">Annuler</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">Supprimer</button>
        </div>
    </div>
</div>

<script>
    // Recherche en temps réel
    document.getElementById('globalSearch')?.addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('.modern-table tbody tr');
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        });
    });
    
    // Suppression
    let deleteId = null;
    
    function confirmDelete(id, name) {
        deleteId = id;
        document.getElementById('deleteMessage').innerHTML = `Supprimer le client <strong>${name}</strong> ?`;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        deleteId = null;
    }
    
    document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
        if(deleteId) window.location.href = `delete.php?id=${deleteId}`;
    });
    
    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if(e.target === this) closeDeleteModal();
    });
    
    // Export CSV
    document.getElementById('exportBtn')?.addEventListener('click', function() {
        let rows = document.querySelectorAll('.modern-table tr');
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
        let a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'clients.csv';
        a.click();
    });
</script>