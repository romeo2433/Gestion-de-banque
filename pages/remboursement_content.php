<?php
$remboursements = $remboursements ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary">
        <i class="fas fa-money-bill-wave me-2"></i>Suivi des Remboursements
    </h2>

    <span class="badge bg-primary p-2">
        Total: <?= count($remboursements) ?>
    </span>
</div>
<div class="card mb-3">
    <div class="card-body">
        <form method="POST" class="row">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control"
                       value="<?= htmlspecialchars($_POST['search'] ?? '') ?>"
                       placeholder="Rechercher...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Rechercher</button>
            </div>
        </form>
    </div>
</div>
<div class="card">
<div class="card-body table-responsive">

<table class="table table-hover">
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

<?php if (!empty($remboursements)): ?>
<?php foreach ($remboursements as $remb): ?>

<?php
$badge = match($remb['statut']) {
    'remboursé' => 'badge-rembourse',
    'en attente' => 'badge-attente',
    'annulé' => 'badge-annule',
    default => 'bg-secondary'
};
?>

<tr>
    <td>#<?= $remb['id'] ?></td>

    <td>
        <strong><?= htmlspecialchars($remb['nom'] . ' ' . $remb['prenom']) ?></strong>
    </td>

    <td class="montant">
        <?= number_format($remb['montant'], 0, ',', ' ') ?> Ar
    </td>

    <td>
        <?= date('d/m/Y H:i', strtotime($remb['date_remboursement'])) ?>
    </td>

    <td>
        <span class="badge-statut <?= $badge ?>">
            <?= ucfirst($remb['statut']) ?>
        </span>
    </td>

    <td>
        <?= htmlspecialchars($remb['agence_nom'] ?? '-') ?>
    </td>

    <td>
        <a href="editremboursement.php?id=<?= $remb['id'] ?>"
           class="btn btn-warning btn-sm">
            <i class="fa fa-edit"></i>
        </a>

        <a href="deleteremboursement.php?id=<?= $remb['id'] ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Supprimer ce remboursement ?')">
            <i class="fa fa-trash"></i>
        </a>
    </td>
</tr>

<?php endforeach; ?>
<?php else: ?>
<tr>
    <td colspan="7" class="text-center text-muted">
        Aucun remboursement trouvé
    </td>
</tr>
<?php endif; ?>

</tbody>
</table>

</div>
</div>