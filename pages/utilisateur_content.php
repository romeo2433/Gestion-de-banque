<h2 class="text-primary mb-4">
    <i class="fas fa-users me-2"></i>Gestion des Clients
</h2>

<div class="mb-3">
    <span class="badge bg-primary p-2">
        Total: <?php echo count($utilisateurs); ?> client(s)
    </span>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Opération réussie</div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="alert alert-info">
    Connecté en tant que : <strong><?php echo $_SESSION['role']; ?></strong>
</div>

<!-- RECHERCHE -->
<div class="card mb-4">
    <div class="card-header">Rechercher</div>
    <div class="card-body">
        <form method="POST" class="row">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control"
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">OK</button>
            </div>
        </form>
    </div>
</div>

<a href="create.php" class="btn btn-success mb-3">Ajouter</a>

<div class="card">
    <div class="card-header">Liste</div>
    <div class="card-body">

        <?php if (empty($utilisateurs)): ?>
            <p>Aucun client</p>
        <?php else: ?>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Agence</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['nom']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['agence_nom']; ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">✏️</a>
                        <a href="delete.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm">🗑️</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php endif; ?>

    </div>
</div>