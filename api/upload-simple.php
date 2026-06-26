<?php
// Desabilitar output de erro HTML
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Tratar preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Método não permitido']);
        exit;
    }

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'Arquivo muito grande (limite do servidor)',
            UPLOAD_ERR_FORM_SIZE => 'Arquivo muito grande (limite do formulário)',
            UPLOAD_ERR_PARTIAL => 'Upload parcial',
            UPLOAD_ERR_NO_FILE => 'Nenhum arquivo enviado',
            UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária não encontrada',
            UPLOAD_ERR_CANT_WRITE => 'Erro ao escrever arquivo',
            UPLOAD_ERR_EXTENSION => 'Extensão PHP bloqueou o upload'
        ];
        
        $errorCode = isset($_FILES['file']) ? $_FILES['file']['error'] : UPLOAD_ERR_NO_FILE;
        $errorMessage = $errorMessages[$errorCode] ?? 'Erro desconhecido no upload';
        
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $errorMessage]);
        exit;
    }

    $file = $_FILES['file'];
    
    // Validar tipo MIME real
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mimeType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Tipo de arquivo não permitido. Use JPG, PNG, GIF ou WebP']);
        exit;
    }
    
    // Validar tamanho (10MB)
    if ($file['size'] > 10 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Arquivo muito grande (máx 10MB)']);
        exit;
    }

    // Criar pasta uploads
    $uploadDir = __DIR__ . '/../uploads/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Não foi possível criar pasta de uploads']);
            exit;
        }
    }

    // Gerar nome único e seguro
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'img_' . uniqid() . '_' . time() . '.' . strtolower($extension);
    $filepath = $uploadDir . $filename;

    // Mover arquivo
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        chmod($filepath, 0644);
        $url = '/uploads/' . $filename;
        
        echo json_encode([
            'success' => true,
            'url' => $url,
            'filename' => $file['name'],
            'size' => $file['size'],
            'type' => $mimeType,
            'message' => 'Upload realizado com sucesso!'
        ]);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao salvar arquivo no servidor']);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro interno: ' . $e->getMessage()
    ]);
}
