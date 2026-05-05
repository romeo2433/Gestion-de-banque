<?php
include 'config.php';

$id = $_GET['id'];

$sql = "DELETE FROM demande_pret WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $id]);

header("Location: demande.php");
exit();
?>
