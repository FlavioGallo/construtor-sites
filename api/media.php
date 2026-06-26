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
$method = $_SERVER['REQUEST_METHOD'];

// GET - Listar todos os arquivos de mídia
if ($method === 'GET') {
    try {
        $stmt = $pdo->prepare('
            SELECT * FROM media 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ');
        $stmt->execute([$userId]);
        $media = $stmt->fetchAll();
        
        echo json_encode($media);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// POST - Upload de arquivo
elseif ($method === 'POST') {
    try {
        if (!isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nenhum arquivo enviado']);
            exit;
        }
        
        $file = $_FILES['file'];
        
        // Validar tipo de arquivo
        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'application/pdf'
        ];
        
        if (!in_array($file['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Tipo de arquivo não permitido']);
            exit;
        }
        
        // Validar tamanho (10MB max)
        if ($file['size'] > 10 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['error' => 'Arquivo muito grande (máx 10MB)']);
            exit;
        }
        
        // Criar pasta de uploads se não existir
        $uploadDir = __DIR__ . '/../uploads/';
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
                INSERT INTO media (user_id, filename, original_name, mime_type, file_size, url, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ');
            
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
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// DELETE - Excluir arquivo
elseif ($method === 'DELETE') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segments = explode('/', $path);
    $mediaId = end($segments);
    
    try {
        // Buscar arquivo
        $stmt = $pdo->prepare('SELECT * FROM media WHERE id = ? AND user_id = ?');
        $stmt->execute([$mediaId, $userId]);
        $media = $stmt->fetch();
        
        if (!$media) {
            http_response_code(404);
            echo json_encode(['error' => 'Arquivo não encontrado']);
            exit;
        }
        
        // Deletar arquivo físico
        $filepath = __DIR__ . '/../' . $media['url'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        
        // Deletar do banco
        $stmt = $pdo->prepare('DELETE FROM media WHERE id = ? AND user_id = ?');
        $stmt->execute([$mediaId, $userId]);
        
        echo json_encode(['success' => true, 'message' => 'Arquivo excluído!']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

else {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
}
