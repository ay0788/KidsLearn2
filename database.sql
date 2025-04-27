-- Création de la base de données
DROP DATABASE IF EXISTS kidslearn;
CREATE DATABASE IF NOT EXISTS kidslearn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kidslearn;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    token VARCHAR(64) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des catégories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-folder',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des éléments
CREATE TABLE IF NOT EXISTS elements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    type VARCHAR(20) NOT NULL,
    content JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Table des médias
CREATE TABLE IF NOT EXISTS media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    type ENUM('image', 'audio', 'video') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des sessions de jeu
CREATE TABLE IF NOT EXISTS game_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_type VARCHAR(50) NOT NULL,
    score INT NOT NULL DEFAULT 0,
    duration INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insertion d'un administrateur par défaut
-- Mot de passe : admin123 (à changer après la première connexion)
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@kidslearn.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Quelques catégories de base
INSERT INTO categories (name, description, icon) VALUES 
('Mathématiques', 'Exercices de mathématiques', 'fas fa-calculator'),
('Français', 'Exercices de français', 'fas fa-book'),
('Sciences', 'Exercices de sciences', 'fas fa-flask'),
('Histoire', 'Exercices d\'histoire', 'fas fa-landmark'); 