<?php
// Activation des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrer la session
session_start();

include 'config.php';

if (isset($_GET['id'])) {
    try {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            throw new Exception("ID invalide");
        }
        
        $sql = "DELETE FROM remboursement WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $_SESSION['message'] = "Remboursement supprimé avec succès";
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "ID non spécifié";
}

header("Location: remboursement.php");
exit();
?>