<?php
session_start();
require_once 'config.php';
require_once 'verification_role.php';

est_connecte();
verifier_acces(['super_admin', 'admin_regional', 'chef_agence', 'agent'], true);

$error = null;
$success = null;
$planification = null;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        // Récupération des données
        $stmt = $conn->prepare("SELECT * FROM planification WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $planification = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$planification) {
            $error = "Planification introuvable.";
        }
        
        // Traitement du formulaire
        if ($_SERVER["REQUEST_METHOD"] == "POST" && $planification) {
            $titre       = trim($_POST['titre']);
            $description = trim($_POST['description']);
            $date_debut  = $_POST['date_debut'];
            $date_fin    = $_POST['date_fin'];
            $statut      = $_POST['statut'];

            // Validation simple
            if (empty($titre) || empty($date_debut) || empty($date_fin)) {
                $error = "Le titre et les dates sont obligatoires.";
            } else {
                $sql = "UPDATE planification 
                        SET titre = :titre, 
                            description = :description,
                            date_debut = :date_debut, 
                            date_fin = :date_fin, 
                            statut = :statut 
                        WHERE id = :id";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'titre'       => $titre,
                    'description' => $description,
                    'date_debut'  => $date_debut,
                    'date_fin'    => $date_fin,
                    'statut'      => $statut,
                    'id'          => $id
                ]);

                journal_action('modification_planification', "ID: $id - Titre: $titre");
                
                header("Location: planification.php?success=2");
                exit();
            }
        }
    } catch(PDOException $e) {
        error_log("Erreur editplanification.php: " . $e->getMessage());
        $error = "Une erreur est survenue lors de la modification.";
    }
} else {
    header("Location: planifications.php");
    exit();
}

// Si on utilise le layout (recommandé)
$content = "pages/editplanification_content.php";
include("layout.php");
?>