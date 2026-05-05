<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM demande_pret WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $id]);

    // Redirige avec un message de succès
    header("Location: demande.php?success=1");
    exit();
} else {
    echo "Aucune demande sélectionnée pour suppression.";
}
?>