<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données de connexion
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nom d\'utilisateur et mot de passe requis']);
    exit;
}

try {
    // Rechercher l'utilisateur dans la base de données
    $stmt = $db->prepare('SELECT id, username, password, role FROM users WHERE username = ? AND role = "admin"');
    $stmt->execute([$data['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($data['password'], $user['password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
        exit;
    }

    // Générer un token JWT (simple pour l'exemple)
    $token = bin2hex(random_bytes(32));

    // Stocker le token dans la base de données
    $stmt = $db->prepare('UPDATE users SET token = ? WHERE id = ?');
    $stmt->execute([$token, $user['id']]);

    echo json_encode([
        'success' => true,
        'message' => 'Connexion réussie',
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ]
    ]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la connexion']);
}
?> 