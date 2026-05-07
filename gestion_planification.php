<?php
session_start();
include 'config.php';

$success = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $titre = trim($_POST['titre']);
        $description = trim($_POST['description']);
        $date_debut = $_POST['date_debut'];
        $date_fin = $_POST['date_fin'];
        $statut = $_POST['statut'];

        if (strtotime($date_fin) < strtotime($date_debut)) {
            throw new Exception("La date de fin doit être après la date de début");
        }

        $sql = "INSERT INTO planification (titre, description, date_debut, date_fin, statut) 
                VALUES (:titre, :description, :date_debut, :date_fin, :statut)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'titre' => $titre,
            'description' => $description,
            'date_debut' => date('Y-m-d H:i:s', strtotime($date_debut)),
            'date_fin' => date('Y-m-d H:i:s', strtotime($date_fin)),
            'statut' => $statut
        ]);

        $success = true;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$content = "pages/ajouter_planification_content.php";
include "layout.php";