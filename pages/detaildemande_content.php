<h2>Détails du prêt</h2>

<div class="card">
    <div class="card-body">

        <p><strong>Client :</strong>
            <?= htmlspecialchars($demande['username']) ?>
        </p>

        <p><strong>Nom :</strong>
            <?= htmlspecialchars($demande['nom'].' '.$demande['prenom']) ?>
        </p>

        <p><strong>Montant :</strong>
            <?= number_format($demande['montant'],0,',',' ') ?> Ar
        </p>

        <p><strong>Durée :</strong>
            <?= $demande['duree'] ?> mois
        </p>

        <p><strong>Taux :</strong>
            <?= $demande['taux_interet'] ?> %
        </p>

        <p><strong>Type :</strong>
            <?= htmlspecialchars($demande['type_pret']) ?>
        </p>

        <p><strong>Revenu :</strong>
            <?= number_format($demande['revenu'],0,',',' ') ?> Ar
        </p>

        <p><strong>Motif :</strong><br>
            <?= nl2br(htmlspecialchars($demande['motif'])) ?>
        </p>

        <p><strong>Statut :</strong>
            <?= htmlspecialchars($demande['statut']) ?>
        </p>

        <a href="espace_client.php" class="btn btn-secondary">
            Retour
        </a>

    </div>
</div>