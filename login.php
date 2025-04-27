<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.email = ? AND u.is_active = TRUE
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role_name'];
            $_SESSION['parent_name'] = $user['parent_name'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Connexion réussie',
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role_name'],
                    'parent_name' => $user['parent_name']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de la connexion']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>