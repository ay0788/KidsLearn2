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
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validation des données
if (empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email et mot de passe requis'
    ]);
    exit;
}

try {
    // Recherche de l'utilisateur dans la base de données
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Création d'un token simple (à améliorer en production)
        $token = bin2hex(random_bytes(32));
        
        // Mise à jour du token dans la base de données
        $updateStmt = $pdo->prepare('UPDATE users SET token = ? WHERE id = ?');
        $updateStmt->execute([$token, $user['id']]);

        echo json_encode([
            'success' => true,
            'message' => 'Connexion réussie',
            'token' => $token
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Email ou mot de passe incorrect'
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la connexion'
    ]);
}
?> 