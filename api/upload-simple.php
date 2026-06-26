<?php
session_start();
header('Content-Type: application/json');

// Permitir CORS se necessário
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    if (!isset($_FILES['file'])) {
        throw new Exception('Nenhum arquivo enviado');
    }

    $file = $_FILES['file'];
    
    // Validar tipo
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Tipo de arquivo não permitido. Use JPG, PNG, GIF ou WebP');
    }
    
    // Validar tamanho (10MB)
    if ($file['size'] > 10 * 1024 * 1024) {
        throw new Exception('Arquivo muito grande (máx 10MB)');
    }

    // Criar pasta uploads
    $uploadDir = __DIR__ . '/../uploads/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Não foi possível criar pasta de uploads');
        }
    }

    // Gerar nome único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'img_' . uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;

    // Mover arquivo
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $url = '/uploads/' . $filename;
        
        echo json_encode([
            'success' => true,
            'url' => $url,
            'filename' => $file['name'],
            'message' => 'Upload realizado com sucesso!'
        ]);
    } else {
        throw new Exception('Erro ao salvar arquivo');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
