<?php
include 'config.php';

// Vérifiez si l'ID est fourni dans l'URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Requête pour supprimer l'utilisateur avec l'ID donné
    $sql = "DELETE FROM utilisateurs WHERE id = :id";  // Changez ici le nom de la table

    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $id]);

    // Redirige vers la page principale après la suppression
    header("Location: utilisateur.php?success=delete");
exit();

} else {
    echo "Aucun utilisateur sélectionné pour suppression.";
}
?>
