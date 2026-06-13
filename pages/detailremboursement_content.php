<h2 class="text-primary mb-4">
    <i class="fas fa-money-bill-wave"></i>
    Détails du remboursement
</h2>

<div class="card">
    <div class="card-body">

        <p>
            <strong>ID :</strong>
            #<?= $remboursement['id'] ?>
        </p>

        <p>
            <strong>Montant :</strong>
            <?= number_format($remboursement['montant'], 0, ',', ' ') ?> Ar
        </p>

        <p>
            <strong>Date de remboursement :</strong>
            <?= date('d/m/Y', strtotime($remboursement['date_remboursement'])) ?>
        </p>

        <p>
            <strong>Statut :</strong>

            <?php
            $badge = 'secondary';

            if ($remboursement['statut'] == 'remboursé') {
                $badge = 'success';
            } elseif ($remboursement['statut'] == 'en attente') {
                $badge = 'warning';
            } elseif ($remboursement['statut'] == 'annulé') {
                $badge = 'danger';
            }
            ?>

            <span class="badge bg-<?= $badge ?>">
                <?= ucfirst($remboursement['statut']) ?>
            </span>
        </p>

        <a href="espace_client.php" class="btn btn-secondary">
            Retour
        </a>

    </div>
</div>