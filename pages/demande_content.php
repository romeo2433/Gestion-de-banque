<h2 class="text-primary mb-4">
    <i class="fas fa-hand-holding-usd me-2"></i>Demandes de Prêt
</h2>

<div class="mb-3">
    <span class="badge bg-primary">
        Total: <?php echo count($demandes); ?> demande(s)
    </span>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="alert alert-info">
    Connecté en tant que : <strong><?php echo $_SESSION['role']; ?></strong>
</div>

<!-- RECHERCHE -->
<div class="card mb-4">
    <div class="card-header">Recherche</div>
    <div class="card-body">
        <form method="GET" class="row">
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

<a href="createdemande.php" class="btn btn-success mb-3">Nouvelle demande</a>

<div class="card">
    <div class="card-header">Liste</div>
    <div class="card-body">

        <?php if (empty($demandes)): ?>
            <p>Aucune demande</p>
        <?php else: ?>

        <table class="table table-hover">
            <thead>
                <tr>
                    
                    <th>Client</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($demandes as $d): ?>
                <tr>
                    
                    <td> <?php echo $d['client_nom']; ?></td>
                    <td><?php echo number_format($d['montant']); ?> Ar</td>
                    <td>

                        <?php if ($d['statut'] == 'approuvé'): ?>

                            <span class="badge bg-success">
                                Approuvé
                            </span>

                        <?php elseif ($d['statut'] == 'refusé'): ?>

                            <span class="badge bg-danger">
                                Refusé
                            </span>

                        <?php else: ?>

                            <span class="badge bg-warning text-dark">
                                En attente
                            </span>

                        <?php endif; ?>

                        </td>
                        <td><?php echo $d['date_demande']; ?></td>
                        <td>
                            <?php if ($d['statut'] == 'en attente'): ?>

                                <form method="POST" style="display:inline;">
    <input type="hidden" name="id" value="<?= $d['id']; ?>">
    <input type="hidden" name="statut" value="approuvé">

    <button type="submit" class="btn btn-success btn-sm">
        ✔ Valider
    </button>
</form>

<button type="button"
        class="btn btn-secondary btn-sm"
        data-bs-toggle="modal"
        data-bs-target="#refusModal<?= $d['id']; ?>">
    ✖ Refuser
</button><div class="modal fade" id="refusModal<?= $d['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST">

                <div class="modal-header">
                    <h5 class="modal-title">Motif du refus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="id" value="<?= $d['id']; ?>">
                    <input type="hidden" name="statut" value="refusé">

                    <label class="form-label">Motif du refus :</label>

                    <textarea name="motif_refus"
                              class="form-control"
                              rows="4"
                              required></textarea>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Annuler
                    </button>

                    <button type="submit"
                            class="btn btn-danger">
                        Confirmer
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
                                <a href="editdemande.php?id=<?= $d['id']; ?>"
                                class="btn btn-warning btn-sm">
                                ✏️
                                </a>

                            <?php else: ?>

                                <span class="text-muted">
                                    Action terminée
                                </span>

                            <?php endif; ?>
                        </td>
                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

        <?php endif; ?>

    </div>
</div>

<a href="dashboard.php" class="btn btn-secondary mt-3">Retour</a>