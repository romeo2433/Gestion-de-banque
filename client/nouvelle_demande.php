<?php

session_start();

require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $utilisateur_id = $_SESSION['user_id'];

    $montant = $_POST['montant'];
    $duree = $_POST['duree'];
    $type_pret = $_POST['type_pret'];
    $revenu = $_POST['revenu'];
    $motif = $_POST['motif'];

    $taux_interet = 5;

    // Gestion upload fichier
    $nom_document = null;

    if (!empty($_FILES['document']['name'])) {

        $nom_document = time() . '_' . $_FILES['document']['name'];

        move_uploaded_file(
            $_FILES['document']['tmp_name'],
            "../uploads/" . $nom_document
        );
    }

    // Insertion
    $stmt = $conn->prepare("
        INSERT INTO demande_pret
        (
            utilisateur_id,
            montant,
            duree,
            taux_interet,
            type_pret,
            revenu,
            motif,
            document,
            statut
        )

        VALUES
        (
            :utilisateur_id,
            :montant,
            :duree,
            :taux_interet,
            :type_pret,
            :revenu,
            :motif,
            :document,
            'en attente'
        )
    ");

    $stmt->execute([

        ':utilisateur_id' => $utilisateur_id,
        ':montant' => $montant,
        ':duree' => $duree,
        ':taux_interet' => $taux_interet,
        ':type_pret' => $type_pret,
        ':revenu' => $revenu,
        ':motif' => $motif,
        ':document' => $nom_document

    ]);

    header("Location: espace_client.php");
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Nouvelle demande de prêt
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body style="background-color:#f8f9fc;">

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow border-0">

                <div class="card-header bg-primary text-white">

                    <h4 class="mb-0">

                        <i class="fas fa-hand-holding-usd me-2"></i>

                        Nouvelle demande de prêt

                    </h4>

                </div>

                <div class="card-body p-4">

                    <form action="traitement_pret.php"
                          method="POST"
                          enctype="multipart/form-data">

                        <div class="row">

                            <!-- Montant -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Montant demandé
                                </label>

                                <input type="number"
                                       name="montant"
                                       class="form-control"
                                       required>

                            </div>

                            <!-- Durée -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Durée du prêt
                                </label>

                                <select name="duree"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        Choisir
                                    </option>

                                    <option value="6">
                                        6 mois
                                    </option>

                                    <option value="12">
                                        12 mois
                                    </option>

                                    <option value="24">
                                        24 mois
                                    </option>

                                    <option value="36">
                                        36 mois
                                    </option>

                                </select>

                            </div>

                        </div>

                        <div class="row">

                            <!-- Type -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Type de prêt
                                </label>

                                <select name="type_pret"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        Sélectionner
                                    </option>

                                    <option value="personnel">
                                        Personnel
                                    </option>

                                    <option value="immobilier">
                                        Immobilier
                                    </option>

                                    <option value="vehicule">
                                        Véhicule
                                    </option>

                                    <option value="etudiant">
                                        Étudiant
                                    </option>

                                </select>

                            </div>

                            <!-- Revenu -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Revenu mensuel
                                </label>

                                <input type="number"
                                       name="revenu"
                                       class="form-control"
                                       required>

                            </div>

                        </div>

                        <!-- Motif -->
                        <div class="mb-3">

                            <label class="form-label">
                                Motif du prêt
                            </label>

                            <textarea name="motif"
                                      rows="4"
                                      class="form-control"
                                      required></textarea>

                        </div>

                        <!-- Fichier -->
                        <div class="mb-4">

                            <label class="form-label">
                                Document justificatif
                            </label>

                            <input type="file"
                                   name="document"
                                   class="form-control">

                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">

                            <a href="../espace_client.php"
                               class="btn btn-secondary">

                                <i class="fas fa-arrow-left"></i>
                                Retour
                            </a>

                            <button type="submit"
                                    class="btn btn-primary">

                                <i class="fas fa-paper-plane"></i>
                                Envoyer la demande

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>