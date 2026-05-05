<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

est_connecte();
verifier_acces(['super_admin', 'admin_regional', 'chef_agence', 'agent'], true);

$search = $_POST['search'] ?? '';
$condition_agence = condition_agence('u');

try {
    $sql = "SELECT u.*, a.nom as agence_nom 
            FROM users u 
            LEFT JOIN agences a ON u.agence_id = a.id_agence
            WHERE $condition_agence";
    
    if (!empty($search)) {
        $sql .= " AND (u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['search' => "%$search%"]);
    } else {
        $stmt = $conn->query($sql);
    }
    
    $utilisateurs = $stmt->fetchAll();
    
    journal_action('consultation_utilisateurs', "Recherche: $search");
    
} catch(PDOException $e) {
    error_log("Erreur utilisateur.php: " . $e->getMessage());
    $error = "Erreur lors du chargement des données.";
}

// 🔥 important
$content = "pages/utilisateur_content.php";
include("layout.php");