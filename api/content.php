<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new content
    $data = json_decode(file_get_contents('php://input'), true);
    
    $title = $data['title'] ?? '';
    $description = $data['description'] ?? '';
    $category_id = $data['category_id'] ?? null;
    $content_type = $data['content_type'] ?? '';
    $content_url = $data['content_url'] ?? '';
    $age_range_min = $data['age_range_min'] ?? null;
    $age_range_max = $data['age_range_max'] ?? null;
    
    // Validate input
    if (empty($title) || empty($content_type)) {
        echo json_encode(['success' => false, 'message' => 'Titre et type de contenu requis']);
        exit;
    }
    
    try {
        // Auto-approve content from admin
        $is_approved = ($user_role === 'admin') ? 1 : 0;
        
        $stmt = $pdo->prepare("
            INSERT INTO content 
            (title, description, category_id, content_type, content_url, age_range_min, age_range_max, created_by, is_approved) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $title,
            $description,
            $category_id,
            $content_type,
            $content_url,
            $age_range_min,
            $age_range_max,
            $user_id,
            $is_approved
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Contenu ajouté avec succès' . ($is_approved ? '' : ' (en attente d\'approbation)')
        ]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du contenu']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get content based on user role
    try {
        if ($user_role === 'admin') {
            // Admin can see all content
            $stmt = $pdo->query("
                SELECT c.*, u.parent_name as creator_name, cat.name as category_name 
                FROM content c 
                LEFT JOIN users u ON c.created_by = u.id 
                LEFT JOIN categories cat ON c.category_id = cat.id 
                ORDER BY c.created_at DESC
            ");
        } else {
            // Regular users can only see approved content
            $stmt = $pdo->query("
                SELECT c.*, u.parent_name as creator_name, cat.name as category_name 
                FROM content c 
                LEFT JOIN users u ON c.created_by = u.id 
                LEFT JOIN categories cat ON c.category_id = cat.id 
                WHERE c.is_approved = 1 
                ORDER BY c.created_at DESC
            ");
        }
        
        $content = $stmt->fetchAll();
        echo json_encode(['success' => true, 'content' => $content]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération du contenu']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT' && $user_role === 'admin') {
    // Approve content (admin only)
    $data = json_decode(file_get_contents('php://input'), true);
    $content_id = $data['content_id'] ?? null;
    
    if (!$content_id) {
        echo json_encode(['success' => false, 'message' => 'ID du contenu requis']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE content SET is_approved = 1 WHERE id = ?");
        $stmt->execute([$content_id]);
        echo json_encode(['success' => true, 'message' => 'Contenu approuvé']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'approbation du contenu']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?> 