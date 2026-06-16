<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

est_connecte();
verifier_acces(['super_admin', 'admin_regional', 'chef_agence', 'agent'], true);

$search = $_POST['search'] ?? '';
$remboursements = [];

try {

    $sql = "SELECT 
                r.*,
                u.username,
                u.agence_id,
                a.nom AS agence_nom
            FROM remboursement r
            JOIN users u ON r.utilisateur_id = u.id
            LEFT JOIN agences a ON u.agence_id = a.id_agence";

    $conditions = [];
    $params = [];

    // filtre agence
    $condition_agence = condition_agence('u');
    if (!empty($condition_agence)) {
        $conditions[] = $condition_agence;
    }

    // recherche
    if (!empty($search)) {
        $conditions[] = "(u.nom LIKE :search 
                        OR u.prenom LIKE :search 
                        OR r.statut LIKE :search)";
        $params['search'] = "%$search%";
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY r.id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $remboursements = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}

$content = "pages/remboursement_content.php";
include "layout.php";