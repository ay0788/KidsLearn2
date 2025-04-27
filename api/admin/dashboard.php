<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
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

    // Récupérer les statistiques
    $stats = [
        'totalUsers' => 0,
        'totalGames' => 0,
        'averageScore' => 0
    ];

    // Nombre total d'utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $stats['totalUsers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Nombre total de parties jouées
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM game_sessions");
    $stats['totalGames'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Score moyen
    $stmt = $pdo->query("SELECT AVG(score) as average FROM game_sessions");
    $stats['averageScore'] = round($stmt->fetch(PDO::FETCH_ASSOC)['average'], 1);

    // Récupérer les activités récentes
    $stmt = $pdo->query("
        SELECT 
            u.username,
            g.name as game,
            gs.score,
            gs.created_at as date
        FROM game_sessions gs
        JOIN users u ON gs.user_id = u.id
        JOIN games g ON gs.game_id = g.id
        ORDER BY gs.created_at DESC
        LIMIT 10
    ");
    $recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formater les dates
    foreach ($recentActivities as &$activity) {
        $activity['date'] = date('d/m/Y H:i', strtotime($activity['date']));
    }

    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'recentActivities' => $recentActivities
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des données',
        'error' => $e->getMessage()
    ]);
}
?> 