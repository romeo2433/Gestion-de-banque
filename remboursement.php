<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

est_connecte();
verifier_acces(['super_admin', 'admin_regional', 'chef_agence', 'agent'], true);

$search = $_POST['search'] ?? '';
$condition_agence = condition_agence('u');

try {

    $sql = "SELECT r.*, u.nom, u.prenom, u.agence_id, a.nom as agence_nom
            FROM remboursement r
            JOIN users u ON r.utilisateur_id = u.id
            LEFT JOIN agences a ON u.agence_id = a.id_agence
            WHERE $condition_agence";

    if (!empty($search)) {
        $sql .= " AND (u.nom LIKE :search 
                    OR u.prenom LIKE :search 
                    OR r.statut LIKE :search)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['search' => "%$search%"]);
    } else {
        $stmt = $conn->query($sql);
    }

    $remboursements = $stmt->fetchAll();

} catch (Exception $e) {
    $remboursements = [];
    $error = "Erreur lors du chargement des données.";
}

$content = "pages/remboursement_content.php";
include "layout.php";