<div class="container-fluid">

    <h2 class="mb-4">Ajouter une Planification</h2>

    <!-- SUCCESS -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            ✅ Planification ajoutée avec succès
        </div>
    <?php endif; ?>

    <!-- ERROR -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <div class="card">
    <div class="card-body">

        <form method="POST">

            <div class="mb-3">
                <label>Titre :</label>
                <input type="text" name="titre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Description :</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label>Date de début :</label>
                <input type="datetime-local" name="date_debut" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Date de fin :</label>
                <input type="datetime-local" name="date_fin" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Statut :</label>
                <select name="statut" class="form-control">
                    <option value="en attente">En attente</option>
                    <option value="en cours">En cours</option>
                    <option value="terminé">Terminé</option>
                </select>
            </div>

            <button class="btn btn-success">Ajouter</button>
            <a href="planification.php" class="btn btn-secondary">Retour</a>

        </form>

    </div>
    </div>

</div>