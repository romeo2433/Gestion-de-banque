<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

// sécurité
est_connecte();
verifier_acces(['super_admin', 'admin_regional', 'chef_agence', 'agent'], true);

try {
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'client'");
    $total_clients = $stmt->fetchColumn();
    
    $stmt = $conn->query("SELECT COUNT(*) FROM demande_pret");
    $total_prets = $stmt->fetchColumn();
    
    $stmt = $conn->query("SELECT SUM(montant) FROM demande_pret");
    $montant_total_prets = $stmt->fetchColumn() ?: 0;

} catch(PDOException $e) {
    $error = "Erreur";
}

// contenu à afficher
$content = "pages/dashboard_content.php";

// charger layout
include("layout.php");