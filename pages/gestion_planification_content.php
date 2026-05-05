<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestion des Planifications</h2>
    <span class="badge bg-primary">
        Total: <?= count($planifications) ?>
    </span>
</div>

<!-- SEARCH -->
<div class="card mb-3">
    <div class="card-body">
        <form method="POST" class="row">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Rechercher</button>
            </div>
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
    <th>Titre</th>
    <th>Description</th>
    <th>Date début</th>
    <th>Date fin</th>
    <th>Statut</th>
    <th>Progression</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>

<?php foreach ($planifications as $p): ?>

<?php
$debut = new DateTime($p['date_debut']);
$fin = new DateTime($p['date_fin']);
$now = new DateTime();

if ($now < $debut) $progress = 0;
elseif ($now > $fin) $progress = 100;
else {
    $total = $debut->diff($fin)->days;
    $ecoule = $debut->diff($now)->days;
    $progress = $total > 0 ? round(($ecoule/$total)*100) : 0;
}
?>

<tr>
    <td>#<?= $p['id'] ?></td>
    <td><?= htmlspecialchars($p['titre']) ?></td>
    <td><?= htmlspecialchars($p['description']) ?></td>
    <td><?= $p['date_debut'] ?></td>
    <td><?= $p['date_fin'] ?></td>
    <td><?= $p['statut'] ?></td>

    <!-- PROGRESSION STYLE PRO -->
    <td style="min-width:150px;">
        <div class="d-flex align-items-center gap-2">
            <small><?= $progress ?>%</small>

            <div class="progress w-100" style="height:8px;">
                <div class="progress-bar 
                    <?= $progress < 30 ? 'bg-danger' : ($progress < 70 ? 'bg-warning' : 'bg-success') ?>"
                    style="width: <?= $progress ?>%">
                </div>
            </div>
        </div>
    </td>

    <td>
        <a href="editplanification.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">
            <i class="fa fa-edit"></i>
        </a>

        <a href="deleteplanification.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm">
            <i class="fa fa-trash"></i>
        </a>
    </td>
</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>
</div>