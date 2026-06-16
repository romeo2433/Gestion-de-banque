<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

est_connecte();
verifier_acces(['super_admin', 'admin_regional', 'chef_agence', 'agent'], true);

$search = $_GET['search'] ?? '';
$planifications = [];

try {

    $sql = "SELECT 
                p.id,
                p.titre,
                p.description,
                p.date_debut,
                p.date_fin,
                p.statut,
                p.utilisateur_id,
                u.username
            FROM planification p
            JOIN users u ON p.utilisateur_id = u.id";

    $conditions = [];
    $params = [];

    if (!empty($search)) {
        $conditions[] = "(p.titre LIKE :search 
                        OR p.description LIKE :search 
                        OR u.username LIKE :search)";
        $params['search'] = "%$search%";
    }

    $condition_agence = condition_agence('u');
    if (!empty($condition_agence)) {
        $conditions[] = $condition_agence;
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY p.id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $planifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}

$content = "pages/planifications_content.php";
include("layout.php");