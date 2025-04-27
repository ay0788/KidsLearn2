<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $parentName = $data['parentName'] ?? '';
    $childName = $data['childName'] ?? '';
    $childAge = $data['childAge'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    
    // Validate input
    if (empty($parentName) || empty($childName) || empty($childAge) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
        exit;
    }
    
    if ($childAge < 3 || $childAge > 10) {
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
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
            exit;
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (parent_name, child_name, child_age, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$parentName, $childName, $childAge, $email, $hashedPassword]);
        
        echo json_encode(['success' => true, 'message' => 'Inscription réussie !']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de l\'inscription']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>