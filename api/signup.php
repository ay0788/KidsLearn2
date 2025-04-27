<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Accept');

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=kidslearn', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de connexion à la base de données'
    ]);
    exit;
}

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

// Récupération des données du formulaire
$parent_name = $_POST['parent_name'] ?? '';
$child_name = $_POST['child_name'] ?? '';
$child_age = $_POST['child_age'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validation des données
if (empty($parent_name) || empty($child_name) || empty($child_age) || empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Tous les champs sont requis'
    ]);
    exit;
}

if ($child_age < 3 || $child_age > 10) {
    echo json_encode(['success' => false, 'message' => 'L\'âge de l\'enfant doit être compris entre 3 et 10 ans']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Veuillez entrer une adresse email valide']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères']);
    exit;
}

try {
    // Vérification si l'email existe déjà
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Cet email est déjà utilisé'
        ]);
        exit;
    }

    // Hashage du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insertion du nouvel utilisateur
    $stmt = $pdo->prepare('INSERT INTO users (parent_name, child_name, child_age, email, password) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$parent_name, $child_name, $child_age, $email, $hashed_password]);

    echo json_encode([
        'success' => true,
        'message' => 'Inscription réussie'
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'inscription'
    ]);
}
?> 