<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

require_once '../config/database.php';

// Vérifier le token d'authentification
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Token manquant']);
    exit;
}

$token = str_replace('Bearer ', '', $headers['Authorization']);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si le token est valide
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE token = ?");
    $stmt->execute([$token]);
    if (!$stmt->fetch()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token invalide']);
        exit;
    }

    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            // Lister les utilisateurs
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $stmt = $pdo->prepare("
                SELECT 
                    id, username, email, created_at,
                    (SELECT COUNT(*) FROM game_sessions WHERE user_id = users.id) as games_played,
                    (SELECT AVG(score) FROM game_sessions WHERE user_id = users.id) as average_score
                FROM users
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Compter le nombre total d'utilisateurs
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            echo json_encode([
                'success' => true,
                'users' => $users,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]);
            break;

        case 'POST':
            // Créer un utilisateur
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Données manquantes']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT)
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'userId' => $pdo->lastInsertId()
            ]);
            break;

        case 'PUT':
            // Modifier un utilisateur
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID utilisateur manquant']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $userId = $_GET['id'];

            $updates = [];
            $params = [];

            if (isset($data['username'])) {
                $updates[] = "username = ?";
                $params[] = $data['username'];
            }
            if (isset($data['email'])) {
                $updates[] = "email = ?";
                $params[] = $data['email'];
            }
            if (isset($data['password'])) {
                $updates[] = "password = ?";
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            if (empty($updates)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Aucune donnée à mettre à jour']);
                exit;
            }

            $params[] = $userId;
            $stmt = $pdo->prepare("UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?");
            $stmt->execute($params);

            echo json_encode([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès'
            ]);
            break;

        case 'DELETE':
            // Supprimer un utilisateur
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID utilisateur manquant']);
                exit;
            }

            $userId = $_GET['id'];

            // Supprimer d'abord les sessions de jeu de l'utilisateur
            $stmt = $pdo->prepare("DELETE FROM game_sessions WHERE user_id = ?");
            $stmt->execute([$userId]);

            // Puis supprimer l'utilisateur
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);

            echo json_encode([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            break;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors du traitement de la requête',
        'error' => $e->getMessage()
    ]);
}
?> 