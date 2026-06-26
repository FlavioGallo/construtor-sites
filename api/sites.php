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

// GET - Listar todos os sites do usuário
if ($method === 'GET') {
    try {
        $stmt = $pdo->prepare('
            SELECT s.*, 
                   COUNT(DISTINCT p.id) as pages_count,
                   (SELECT COUNT(*) FROM elements e 
                    JOIN pages p ON e.page_id = p.id 
                    WHERE p.site_id = s.id) as elements_count
            FROM sites s
            LEFT JOIN pages p ON s.id = p.site_id
            WHERE s.user_id = ?
            GROUP BY s.id
            ORDER BY s.updated_at DESC
        ');
        $stmt->execute([$userId]);
        $sites = $stmt->fetchAll();
        
        echo json_encode($sites);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// POST - Criar novo site
elseif ($method === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || !isset($data['slug'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nome e slug são obrigatórios']);
            exit;
        }
        
        $name = trim($data['name']);
        $slug = trim($data['slug']);
        $template = $data['template'] ?? 'blank';
        
        // Verificar se slug já existe
        $stmt = $pdo->prepare('SELECT id FROM sites WHERE slug = ?');
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Este slug já está em uso']);
            exit;
        }
        
        // Criar site
        $stmt = $pdo->prepare('
            INSERT INTO sites (user_id, name, slug, template, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ');
        $stmt->execute([$userId, $name, $slug, $template]);
        
        $siteId = $pdo->lastInsertId();
        
        // Criar página inicial se for template blank
        if ($template === 'blank') {
            $stmt = $pdo->prepare('
                INSERT INTO pages (site_id, name, slug, is_home, created_at, updated_at)
                VALUES (?, ?, ?, 1, NOW(), NOW())
            ');
            $stmt->execute([$siteId, 'Página Inicial', 'home']);
        } else {
            // Carregar template (implementar depois)
            loadTemplate($siteId, $template);
        }
        
        echo json_encode([
            'success' => true,
            'site_id' => $siteId,
            'message' => 'Site criado com sucesso!'
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// DELETE - Excluir site
elseif ($method === 'DELETE') {
    // Extrair ID da URL
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segments = explode('/', $path);
    $siteId = end($segments);
    
    try {
        $stmt = $pdo->prepare('DELETE FROM sites WHERE id = ? AND user_id = ?');
        $stmt->execute([$siteId, $userId]);
        
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Site não encontrado']);
            exit;
        }
        
        echo json_encode(['success' => true, 'message' => 'Site excluído!']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

else {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
}

function loadTemplate($siteId, $template) {
    global $pdo;
    
    // Templates básicos (pode expandir depois)
    $templates = [
        'restaurant' => [
            'pages' => [
                ['name' => 'Home', 'slug' => 'home', 'is_home' => 1],
                ['name' => 'Cardápio', 'slug' => 'cardapio', 'is_home' => 0],
                ['name' => 'Contato', 'slug' => 'contato', 'is_home' => 0]
            ]
        ],
        'portfolio' => [
            'pages' => [
                ['name' => 'Home', 'slug' => 'home', 'is_home' => 1],
                ['name' => 'Projetos', 'slug' => 'projetos', 'is_home' => 0],
                ['name' => 'Sobre', 'slug' => 'sobre', 'is_home' => 0]
            ]
        ],
        'store' => [
            'pages' => [
                ['name' => 'Home', 'slug' => 'home', 'is_home' => 1],
                ['name' => 'Produtos', 'slug' => 'produtos', 'is_home' => 0],
                ['name' => 'Carrinho', 'slug' => 'carrinho', 'is_home' => 0]
            ]
        ]
    ];
    
    if (!isset($templates[$template])) {
        return;
    }
    
    $templateData = $templates[$template];
    
    foreach ($templateData['pages'] as $pageData) {
        $stmt = $pdo->prepare('
            INSERT INTO pages (site_id, name, slug, is_home, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ');
        $stmt->execute([
            $siteId,
            $pageData['name'],
            $pageData['slug'],
            $pageData['is_home']
        ]);
    }
}
