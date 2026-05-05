<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

est_connecte();
verifier_acces(['super_admin', 'admin_regional', 'chef_agence', 'agent'], true);

$search = $_GET['search'] ?? '';   // Changé en GET (meilleur pour la recherche)
$condition_agence = condition_agence(); 
$error = null;
$planifications = [];

try {
    if (!empty($search)) {
        $sql = "SELECT p.* FROM planification p 
                WHERE (p.titre LIKE :search OR p.description LIKE :search)";
        
        // Si l'utilisateur n'est pas super_admin, on filtre par agence
        if (!empty($condition_agence)) {
            $sql .= " AND " . $condition_agence;
        }
        
        $sql .= " ORDER BY p.date_debut DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute(['search' => "%$search%"]);
    } else {
        $sql = "SELECT p.* FROM planification p";
        
        if (!empty($condition_agence)) {
            $sql .= " WHERE " . $condition_agence;
        }
        
        $sql .= " ORDER BY p.date_debut DESC";
        
        $stmt = $conn->query($sql);
    }
    
    $planifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    journal_action('consultation_planifications', "Recherche: " . $search);

} catch(PDOException $e) {
    error_log("Erreur planifications.php: " . $e->getMessage());
    $error = "Erreur lors du chargement des planifications.";
}

// Chargement via le layout (recommandé)
$content = "pages/planifications_content.php";
include("layout.php");