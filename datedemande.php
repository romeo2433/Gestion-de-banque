<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

est_connecte();

$error = '';
$message = '';

// ID obligatoire
if (!isset($_GET['id'])) {
    header('Location: demande.php');
    exit;
}

$id = intval($_GET['id']);

try {

    // 🔹 Récupération demande
    $stmt = $conn->prepare("
        SELECT *
        FROM demande_pret
        WHERE id = :id
    ");
    $stmt->execute(['id' => $id]);
    $demande = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$demande) {
        die("Demande introuvable.");
    }

    // 🔴 Sécurité : vérifier propriétaire (IMPORTANT)
    //if ($demande['utilisateur_id'] != $_SESSION['utilisateur_id']) {
      //  die("Accès interdit.");
    //}

    // 🔴 Empêcher modification si déjà traitée
    if ($demande['statut'] !== 'en attente') {
        die("Cette demande ne peut plus être modifiée.");
    }

    // 🔹 CSRF token
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // 🔹 Traitement formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // CSRF check
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Requête invalide (CSRF).");
        }

        // Validation des données
        $montant = floatval($_POST['montant']);
        $duree = intval($_POST['duree']);
        $taux_interet = floatval($_POST['taux_interet']);
        $type_pret = trim($_POST['type_pret']);
        $revenu = floatval($_POST['revenu']);
        $motif = trim($_POST['motif']);

        // Update
        $update = $conn->prepare("
            UPDATE demande_pret
            SET
                montant = :montant,
                duree = :duree,
                taux_interet = :taux_interet,
                type_pret = :type_pret,
                revenu = :revenu,
                motif = :motif
            WHERE id = :id
        ");

        $update->execute([
            'montant' => $montant,
            'duree' => $duree,
            'taux_interet' => $taux_interet,
            'type_pret' => $type_pret,
            'revenu' => $revenu,
            'motif' => $motif,
            'id' => $id
        ]);

        // Journalisation
        journal_action(
            'modification_demande_pret',
            "Modification demande #$id"
        );

        $message = "Demande modifiée avec succès.";

        // recharger
        $stmt->execute(['id' => $id]);
        $demande = $stmt->fetch(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $error = "Erreur : " . $e->getMessage();
}

// Vue
$content = "pages/editdemande_content.php";
include("layout.php");