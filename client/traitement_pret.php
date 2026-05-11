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