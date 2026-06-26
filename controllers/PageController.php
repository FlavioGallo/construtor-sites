<?php
class PageController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function editor($pageId) {
        include '/var/www/html/views/editor.php';
    }
    
    public function saveElements() {
        header('Content-Type: application/json');
        
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'JSON inválido']);
                return;
            }
            
            if (!isset($data['elements']) || !isset($data['pageId'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
                return;
            }
            
            $pageId = (int)$data['pageId'];
            $elements = $data['elements'];
            
            // 1. Criar usuário padrão se não existir
            $stmt = $this->pdo->query('SELECT id FROM users LIMIT 1');
            $user = $stmt->fetch();
            
            if (!$user) {
                $this->pdo->exec("
                    INSERT INTO users (id, name, email, password, created_at) 
                    VALUES (1, 'Admin', 'admin@site.com', 'senha123', NOW())
                ");
            }
            $userId = 1;
            
            // 2. Criar site padrão se não existir
            $stmt = $this->pdo->query('SELECT id FROM sites LIMIT 1');
            $site = $stmt->fetch();
            
            if (!$site) {
                $this->pdo->exec("
                    INSERT INTO sites (id, user_id, name, slug, is_published, created_at) 
                    VALUES (1, {$userId}, 'Meu Site', 'meu-site', 1, NOW())
                ");
            }
            $siteId = 1;
            
            // 3. Criar página se não existir
            $stmt = $this->pdo->prepare('SELECT id FROM pages WHERE id = ?');
            $stmt->execute([$pageId]);
            $page = $stmt->fetch();
            
            if (!$page) {
                $stmt = $this->pdo->prepare('
                    INSERT INTO pages (id, site_id, name, slug) 
                    VALUES (?, ?, "Página Inicial", "home")
                ');
                $stmt->execute([$pageId, $siteId]);
            }
            
            // 4. Deletar elementos antigos
            $stmt = $this->pdo->prepare('DELETE FROM elements WHERE page_id = ?');
            $stmt->execute([$pageId]);
            
            // 5. Inserir novos elementos
            $stmt = $this->pdo->prepare('
                INSERT INTO elements (page_id, type, content, pos_x, pos_y, width, height, styles)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');
            
            foreach ($elements as $element) {
                $stmt->execute([
                    $pageId,
                    $element['type'] ?? 'text',
                    $element['content'] ?? '',
                    $element['x'] ?? 0,
                    $element['y'] ?? 0,
                    $element['width'] ?? 100,
                    $element['height'] ?? 50,
                    json_encode($element['styles'] ?? new stdClass())
                ]);
            }
            
            echo json_encode(['success' => true, 'message' => count($elements) . ' elementos salvos!']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    public function getElements($pageId) {
        $stmt = $this->pdo->prepare('SELECT * FROM elements WHERE page_id = ?');
        $stmt->execute([$pageId]);
        return $stmt->fetchAll();
    }
}
