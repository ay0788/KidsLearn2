<?php
// Paramètres de connexion à la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // À modifier selon votre configuration
define('DB_PASS', '');         // À modifier selon votre configuration
define('DB_NAME', 'kidslearn');

try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch(PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}
?> 