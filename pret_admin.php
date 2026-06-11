<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

est_connecte();
verifier_acces(['super_admin', 'admin_regional', 'chef_agence', 'agent'], true);

$message = '';
$error = '';

try {
    $users = $conn->query("
        SELECT id, username
        FROM users
        ORDER BY username
    ")->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
                statut,
                date_demande
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
                '',
                'en attente',
                NOW()
            )
        ");

        $stmt->execute([
            'utilisateur_id' => $_POST['utilisateur_id'],
            'montant' => $_POST['montant'],
            'duree' => $_POST['duree'],
            'taux_interet' => $_POST['taux_interet'],
            'type_pret' => $_POST['type_pret'],
            'revenu' => $_POST['revenu'],
            'motif' => $_POST['motif']
        ]);

        $demandeId = $conn->lastInsertId();
        $message = "Prêt créé avec succès.";
    }

} catch(Exception $e) {
    $error = $e->getMessage();
}

$content = "pages/pret_admin_content.php";
include("layout.php");