<?php
// ====================== CONFIGURATION BASE DE DONNÉES ======================
$host       = "localhost";
$dbname     = "myapp";
$db_username = "root";
$db_password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// ====================== CONFIGURATION reCAPTCHA ======================
define('RECAPTCHA_SITE_KEY', '6LdL8NEsAAAAAOWTO8j6_qmq2XFy084Bp_j_8AYp');
define('RECAPTCHA_SECRET_KEY', '6LdL8NEsAAAAAO87znAuGbQJ1hAfJOA8Tb7wTdvc');

// ====================== AUTRES CONFIGURATIONS ======================
define('SITE_NAME', 'IGOR Banking');
?>