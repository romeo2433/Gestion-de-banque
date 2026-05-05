<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

est_connecte();
verifier_acces(['super_admin', 'admin_regional', 'chef_agence', 'agent'], true);

$search = $_GET['search'] ?? '';
$condition_agence = condition_agence('u');

try {
    $sql = "SELECT dp.*, u.nom, u.prenom, u.agence_id, a.nom as agence_nom 
            FROM demande_pret dp
            JOIN users u ON dp.utilisateur_id = u.id
            LEFT JOIN agences a ON u.agence_id = a.id_agence
            WHERE $condition_agence";
    
    if (!empty($search)) {
        $sql .= " AND (u.nom LIKE :search OR u.prenom LIKE :search OR dp.montant LIKE :search)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['search' => "%$search%"]);
    } else {
        $stmt = $conn->query($sql);
    }
    
    $demandes = $stmt->fetchAll();

    journal_action('consultation_demandes_pret', "Recherche: $search");

} catch(PDOException $e) {
    $error = "Erreur lors du chargement des données.";
}

// très important
$content = "pages/demande_content.php";
include("layout.php");