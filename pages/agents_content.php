<div class="container mt-4">

<h3>Gestion Agents Bancaires</h3>

<!-- messages -->
<?php if($success_message): ?>
<div class="alert alert-success"><?= $success_message ?></div>
<?php endif; ?>

<?php if($error_message): ?>
<div class="alert alert-danger"><?= $error_message ?></div>
<?php endif; ?>

<!-- FORM -->
<div class="card mb-3">
<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6">
<input type="text" name="nom" class="form-control" placeholder="Nom">
</div>

<div class="col-md-6">
<input type="text" name="prenom" class="form-control" placeholder="Prénom">
</div>

<div class="col-md-6 mt-2">
<input type="email" name="email" class="form-control" placeholder="Email">
</div>

<div class="col-md-6 mt-2">
<input type="text" name="telephone" class="form-control" placeholder="Téléphone">
</div>

<div class="col-md-6 mt-2">
<input type="date" name="date_embauche" class="form-control">
</div>

<div class="col-md-6 mt-2">
<select name="agence" class="form-control">
<option value="">Agence</option>
<?php foreach($agences as $a): ?>
<option value="<?= $a['nom'] ?>"><?= $a['nom'] ?></option>
<?php endforeach; ?>
</select>
</div>

</div>

<button class="btn btn-success mt-3" name="ajouter">Ajouter</button>

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
<th>Email</th>
<th>Téléphone</th>
<th>Agence</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php foreach($agents as $a): ?>
<tr>
<td>#<?= $a['id'] ?></td>
<td><?= $a['nom'].' '.$a['prenom'] ?></td>
<td><?= $a['email'] ?></td>
<td><?= $a['telephone'] ?></td>
<td><?= $a['agence'] ?></td>

<td>
<a href="?supprimer=<?= $a['id'] ?>" class="btn btn-danger btn-sm">
Delete
</a>
</td>

</tr>
<?php endforeach; ?>

</tbody>

</table>

</div>
</div>

</div>