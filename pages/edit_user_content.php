<div class="form-card">
<h2 class="mb-4">Modifier un utilisateur</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST">

    <div class="mb-3">
        <label>Nom</label>
        <input type="text" name="nom" class="form-control"
               value="<?= htmlspecialchars($user['nom']) ?>">
    </div>

    <div class="mb-3">
        <label>Prénom</label>
        <input type="text" name="prenom" class="form-control"
               value="<?= htmlspecialchars($user['prenom']) ?>">
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control"
               value="<?= htmlspecialchars($user['email']) ?>">
    </div>

    <div class="mb-3">
        <label>Téléphone</label>
        <input type="text" name="telephone" class="form-control"
               value="<?= htmlspecialchars($user['telephone']) ?>">
    </div>

    <div class="mb-3">
        <label>Mot de passe</label>
        <input type="password" name="mot_de_passe" class="form-control">
        <small>Laisser vide pour ne pas changer</small>
    </div>

    <button class="btn btn-primary">Modifier</button>
    <a href="utilisateur.php" class="btn btn-secondary">Retour</a>

</form>
</div>