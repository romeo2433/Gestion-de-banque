<?php
/**
 * VÉRIFICATION DES RÔLES ET PERMISSIONS
 * Fichier central pour la sécurité multi-rôles
 */

// Vérifier si l'utilisateur est connecté
function est_connecte() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    return true;
}

// Vérifier le rôle
function a_le_role($roles_autorises) {
    if (!isset($_SESSION['role'])) return false;
    return in_array($_SESSION['role'], (array)$roles_autorises);
}

// Vérifier l'accès complet
function verifier_acces($roles_autorises, $besoin_agence = false) {
    global $conn;
    
    // 1. Vérifier connexion
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    
    $role = $_SESSION['role'];
    
    // 2. Vérifier rôle
    if (!in_array($role, (array)$roles_autorises)) {
        header("Location: acces_refuse.php");
        exit();
    }
    
    // 3. Si besoin agence et pas super_admin, vérifier agence_id
    if ($besoin_agence && $role != 'super_admin') {
        try {
            $stmt = $conn->prepare("SELECT agence_id FROM users WHERE id = :id");
            $stmt->execute([':id' => $_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || !$user['agence_id']) {
                header("Location: acces_refuse.php?erreur=agence");
                exit();
            }
            
            $_SESSION['agence_id'] = $user['agence_id'];
            
        } catch(PDOException $e) {
            error_log("Erreur vérification agence: " . $e->getMessage());
            header("Location: acces_refuse.php?erreur=technique");
            exit();
        }
    }
    
    return true;
}

// Obtenir la condition SQL pour filtrer par agence
function condition_agence($table_alias = '') {
    if ($_SESSION['role'] == 'super_admin') {
        return "1=1"; // Super admin voit tout
    }
    
    if (!isset($_SESSION['agence_id'])) {
        return "1=0"; // Pas d'agence = pas de données
    }
    
    $alias = $table_alias ? $table_alias . '.' : '';
    return $alias . "agence_id = " . intval($_SESSION['agence_id']);
}

// Journalisation des actions
function journal_action($action, $details = '') {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) return;
    
    try {
        $sql = "INSERT INTO audit (table_concernee, action_effectuee, id_enregistrement, id_utilisateur, type_utilisateur, details, adresse_ip) 
                VALUES ('session', :action, 0, :user_id, :role, :details, :ip)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':action' => $action,
            ':user_id' => $_SESSION['user_id'],
            ':role' => $_SESSION['role'] ?? 'inconnu',
            ':details' => $details,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
        ]);
    } catch(PDOException $e) {
        error_log("Erreur journalisation: " . $e->getMessage());
    }
}

// Vérifier si peut modifier
function peut_modifier($proprietaire_id) {
    if ($_SESSION['role'] == 'super_admin') return true;
    if ($_SESSION['user_id'] == $proprietaire_id) return true; // Son propre compte
    return false;
}
?>