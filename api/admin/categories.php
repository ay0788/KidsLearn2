<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/database.php';

// Vérifier le token d'authentification
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Token manquant']);
    exit;
}

$token = str_replace('Bearer ', '', $headers['Authorization']);
// TODO: Ajouter la vérification du token avec la base de données

// Traiter la requête selon la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Récupérer toutes les catégories
        try {
            $stmt = $db->query('SELECT * FROM categories ORDER BY name');
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'categories' => $categories]);
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des catégories']);
        }
        break;

    case 'POST':
        // Ajouter une nouvelle catégorie
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Le nom de la catégorie est requis']);
            exit;
        }

        try {
            $stmt = $db->prepare('INSERT INTO categories (name, description, icon) VALUES (?, ?, ?)');
            $stmt->execute([
                $data['name'],
                $data['description'] ?? '',
                $data['icon'] ?? 'fas fa-folder'
            ]);

            $categoryId = $db->lastInsertId();
            echo json_encode([
                'success' => true,
                'message' => 'Catégorie créée avec succès',
                'category' => [
                    'id' => $categoryId,
                    'name' => $data['name'],
                    'description' => $data['description'] ?? '',
                    'icon' => $data['icon'] ?? 'fas fa-folder'
                ]
            ]);
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la création de la catégorie']);
        }
        break;

    case 'DELETE':
        // Supprimer une catégorie
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID de catégorie manquant']);
            exit;
        }

        try {
            $stmt = $db->prepare('DELETE FROM categories WHERE id = ?');
            $stmt->execute([$_GET['id']]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Catégorie supprimée avec succès']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Catégorie non trouvée']);
            }
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de la catégorie']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
        break;
} 