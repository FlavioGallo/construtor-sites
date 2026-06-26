<?php
session_start();
header('Content-Type: application/json');

// Verificar se está logado (vamos implementar auth depois)
// if (!isset($_SESSION['user_id'])) {
//     http_response_code(401);
//     echo json_encode(['error' => 'Não autorizado']);
//     exit;
}

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Nenhum arquivo enviado']);
    exit;
}

$file = $_FILES['file'];

// Validar tipo de arquivo
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
if (!in_array($file['type'], $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Tipo de arquivo não permitido']);
    exit;
}

// Validar tamanho (5MB max)
if ($file['size'] > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['error' => 'Arquivo muito grande (máx 5MB)']);
    exit;
}

// Criar pasta de uploads se não existir
$uploadDir = '/var/www/html/uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Gerar nome único
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '_' . time() . '.' . $extension;
$filepath = $uploadDir . $filename;

// Mover arquivo
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // Salvar no banco
    $stmt = $pdo->prepare('
        INSERT INTO media (user_id, filename, original_name, mime_type, file_size, url)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    
    $userId = $_SESSION['user_id'] ?? 1; // Temporário
    $url = '/uploads/' . $filename;
    
    $stmt->execute([
        $userId,
        $filename,
        $file['name'],
        $file['type'],
        $file['size'],
        $url
    ]);
    
    $mediaId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'id' => $mediaId,
        'url' => $url,
        'filename' => $file['name']
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao fazer upload']);
}