<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

require_once __DIR__ . '/../../config/database.php';

// Vérifier le token d'authentification
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Token manquant']);
    exit;
}

$token = str_replace('Bearer ', '', $headers['Authorization']);

try {
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
            // Lister les éléments avec leurs catégories
            $stmt = $pdo->query("
                SELECT 
                    e.*,
                    c.name as category_name
                FROM elements e
                LEFT JOIN categories c ON e.category_id = c.id
                ORDER BY e.created_at DESC
            ");
            $elements = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'elements' => $elements
            ]);
            break;

        case 'POST':
            // Créer un élément
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['name']) || !isset($data['type'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nom et type requis']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO elements (name, category_id, type, content) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['name'],
                $data['category_id'] ?? null,
                $data['type'],
                $data['content'] ?? ''
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Élément créé avec succès',
                'elementId' => $pdo->lastInsertId()
            ]);
            break;

        case 'PUT':
            // Modifier un élément
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID élément manquant']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $elementId = $_GET['id'];

            $updates = [];
            $params = [];

            if (isset($data['name'])) {
                $updates[] = "name = ?";
                $params[] = $data['name'];
            }
            if (isset($data['category_id'])) {
                $updates[] = "category_id = ?";
                $params[] = $data['category_id'];
            }
            if (isset($data['type'])) {
                $updates[] = "type = ?";
                $params[] = $data['type'];
            }
            if (isset($data['content'])) {
                $updates[] = "content = ?";
                $params[] = $data['content'];
            }

            if (empty($updates)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Aucune donnée à mettre à jour']);
                exit;
            }

            $params[] = $elementId;
            $stmt = $pdo->prepare("UPDATE elements SET " . implode(", ", $updates) . " WHERE id = ?");
            $stmt->execute($params);

            echo json_encode([
                'success' => true,
                'message' => 'Élément mis à jour avec succès'
            ]);
            break;

        case 'DELETE':
            // Supprimer un élément
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID élément manquant']);
                exit;
            }

            $elementId = $_GET['id'];

            $stmt = $pdo->prepare("DELETE FROM elements WHERE id = ?");
            $stmt->execute([$elementId]);

            echo json_encode([
                'success' => true,
                'message' => 'Élément supprimé avec succès'
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            break;
    }

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors du traitement de la requête',
        'error' => $e->getMessage()
    ]);
}
?> 