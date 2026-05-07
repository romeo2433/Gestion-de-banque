<div class="d-flex justify-content-between mb-4">
    <h2>Gestion des Utilisateurs</h2>
    <span class="badge bg-primary">
        Total: <?= count($utilisateurs) ?>
    </span>
</div>

<!-- MESSAGE -->
<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<!-- FORMULAIRE -->
<div class="card mb-4">
    <div class="card-header">Ajouter un utilisateur</div>
    <div class="card-body">

        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <input type="text" name="nom" class="form-control" placeholder="Nom" required>
                </div>

                <div class="col-md-6 mb-3">
                    <input type="text" name="prenom" class="form-control" placeholder="Prénom" required>
                </div>

                <div class="col-md-6 mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>

                <div class="col-md-6 mb-3">
                    <input type="text" name="telephone" class="form-control" placeholder="Téléphone">
                </div>

                <div class="col-md-6 mb-3">
                    <input type="password" name="mot_de_passe" class="form-control" placeholder="Mot de passe" required>
                </div>
            </div>

            <button class="btn btn-success">Ajouter</button>
        </form>

    </div>
</div>

<!-- TABLE -->
<div class="card">
<div class="card-body table-responsive">

<table class="table table-hover">
<thead>
<tr>
    <th>ID</th>
    <th>Nom</th>
    <th>Prénom</th>
    <th>Email</th>
    <th>Téléphone</th>
</tr>
</thead>

<tbody>

<?php foreach ($utilisateurs as $u): ?>
<tr>
    <td>#<?= $u['id'] ?></td>
    <td><?= htmlspecialchars($u['nom']) ?></td>
    <td><?= htmlspecialchars($u['prenom']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>
    <td><?= htmlspecialchars($u['telephone']) ?></td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

</div>
</div>