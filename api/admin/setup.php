<?php
require_once __DIR__ . '/../../config/database.php';

try {
    // Créer la table admins si elle n'existe pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        token VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Créer la table categories si elle n'existe pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        icon VARCHAR(50) DEFAULT 'fas fa-folder',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Créer la table elements si elle n'existe pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS elements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category_id INT,
        type ENUM('text', 'image', 'audio', 'video') NOT NULL,
        content TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )");

    // Créer la table media si elle n'existe pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS media (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        type ENUM('image', 'audio', 'video') NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Créer un admin par défaut si la table est vide
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    if ($stmt->fetchColumn() == 0) {
        $defaultUsername = 'admin';
        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute([$defaultUsername, $defaultPassword]);
        
        echo "Admin par défaut créé :\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
    } else {
        echo "La table admins existe déjà et contient des données.\n";
    }

    echo "Configuration terminée avec succès.\n";
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?> 