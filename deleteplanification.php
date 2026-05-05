<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM planification WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $id]);

    header("Location: planification.php?success=1");
    exit();
} else {
    echo "Aucune planification sélectionnée pour suppression.";
}
?>