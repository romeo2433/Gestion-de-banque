<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

$utilisateur_id = $_SESSION['user_id'];
$montant        = floatval($_POST['montant']);
$duree          = intval($_POST['duree']);
$type_pret      = htmlspecialchars(trim($_POST['type_pret']));
$revenu         = floatval($_POST['revenu']);
$motif          = htmlspecialchars(trim($_POST['motif'] ?? ''));

// === VALIDATIONS ===
$erreurs = [];

if ($montant < 100000) {
    $erreurs[] = "Le montant minimum est de 100 000 Ar";
}
if ($montant > 10000000000) { // 10 milliards
    $erreurs[] = "Le montant maximum autorisé est de 10 000 000 000 Ar";
}
if ($duree < 6 || $duree > 60) {
    $erreurs[] = "La durée doit être entre 6 et 60 mois";
}
if (empty($type_pret)) {
    $erreurs[] = "Veuillez sélectionner un type de prêt";
}

if (!empty($erreurs)) {
    $_SESSION['erreurs'] = $erreurs;
    header("Location: ../nouvelle_demande.php");
    exit();
}

// Taux d'intérêt
$taux_interet = match(strtolower($type_pret)) {
    'immobilier' => 5.00,
    'vehicule'   => 7.00,
    'personnel'  => 10.00,
    'etudiant'   => 3.00,
    default      => 8.00,
};

// Upload document
$nom_document = null;
if (!empty($_FILES['document']['name'])) {
    $extensions = ['pdf', 'jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $extensions)) {
        $nom_document = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dossier = "../uploads/";

        if (!is_dir($dossier)) {
            mkdir($dossier, 0777, true);
        }

        if (!move_uploaded_file($_FILES['document']['tmp_name'], $dossier . $nom_document)) {
            $_SESSION['erreurs'][] = "Erreur lors de l'upload du document.";
            header("Location: ../nouvelle_demande.php");
            exit();
        }
    } else {
        $_SESSION['erreurs'][] = "Format de fichier non autorisé (PDF, JPG, PNG seulement)";
        header("Location: ../nouvelle_demande.php");
        exit();
    }
}

// Insertion
try {
    $sql = "INSERT INTO demande_pret 
            (utilisateur_id, montant, duree, taux_interet, type_pret, revenu, motif, document, statut) 
            VALUES 
            (:uid, :montant, :duree, :taux, :type, :revenu, :motif, :doc, 'en attente')";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':uid'     => $utilisateur_id,
        ':montant' => $montant,
        ':duree'   => $duree,
        ':taux'    => $taux_interet,
        ':type'    => $type_pret,
        ':revenu'  => $revenu,
        ':motif'   => $motif,
        ':doc'     => $nom_document
    ]);

    $_SESSION['success'] = " Votre demande de prêt a été envoyée avec succès !";
    header("Location: ../../espace_client.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['erreurs'][] = "Erreur technique : " . $e->getMessage();
    header("Location: ../nouvelle_demande.php");
    exit();
}
?>