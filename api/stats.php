<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

// Verificar autenticação
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Total de sites
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM sites WHERE user_id = ?');
    $stmt->execute([$userId]);
    $sites = $stmt->fetch()['count'];
    
    // Total de páginas
    $stmt = $pdo->prepare('
        SELECT COUNT(*) as count 
        FROM pages p
        JOIN sites s ON p.site_id = s.id
        WHERE s.user_id = ?
    ');
    $stmt->execute([$userId]);
    $pages = $stmt->fetch()['count'];
    
    // Total de arquivos de mídia
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM media WHERE user_id = ?');
    $stmt->execute([$userId]);
    $media = $stmt->fetch()['count'];
    
    echo json_encode([
        'sites' => $sites,
        'pages' => $pages,
        'media' => $media
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
