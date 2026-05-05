<div class="container mt-4">

    <h3>Modifier Remboursement</h3>

    <!-- messages -->
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">

            <form method="POST">

                <input type="hidden" name="id" value="<?= $remboursement['id'] ?>">

                <!-- USER -->
                <div class="mb-3">
                    <label>Utilisateur</label>
                    <select name="utilisateur_id" class="form-control" required>
                        <?php foreach($utilisateurs as $u): ?>
                            <option value="<?= $u['id'] ?>"
                                <?= $remboursement['utilisateur_id'] == $u['id'] ? 'selected' : '' ?>>
                                <?= $u['nom'] . ' ' . $u['prenom'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- MONTANT -->
                <div class="mb-3">
                    <label>Montant</label>
                    <input type="number" name="montant" class="form-control"
                           value="<?= $remboursement['montant'] ?>" required>
                </div>

                <!-- DATE -->
                <div class="mb-3">
                    <label>Date remboursement</label>
                    <input type="datetime-local" name="date_remboursement"
                           class="form-control"
                           value="<?= date('Y-m-d\TH:i', strtotime($remboursement['date_remboursement'])) ?>"
                           required>
                </div>

                <!-- STATUT -->
                <div class="mb-3">
                    <label>Statut</label>
                    <select name="statut" class="form-control">
                        <option value="en attente" <?= $remboursement['statut']=='en attente'?'selected':'' ?>>En attente</option>
                        <option value="remboursé" <?= $remboursement['statut']=='remboursé'?'selected':'' ?>>Remboursé</option>
                        <option value="annulé" <?= $remboursement['statut']=='annulé'?'selected':'' ?>>Annulé</option>
                    </select>
                </div>

                <button class="btn btn-primary">Modifier</button>
                <a href="remboursement.php" class="btn btn-secondary">Retour</a>

            </form>

        </div>
    </div>

</div>