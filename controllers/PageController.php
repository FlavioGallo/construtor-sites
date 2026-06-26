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
                echo json_encode(['success' => false, 'error' => 'Dados inválidos. Esperado: pageId e elements']);
                return;
            }
            
            $pageId = (int)$data['pageId'];
            $elements = $data['elements'];
            
            // Criar página se não existir
            $stmt = $this->pdo->prepare('INSERT IGNORE INTO pages (id, site_id, name, slug) VALUES (?, 1, "Página 1", "home")');
            $stmt->execute([$pageId]);
            
            // Deletar elementos antigos
            $stmt = $this->pdo->prepare('DELETE FROM elements WHERE page_id = ?');
            $stmt->execute([$pageId]);
            
            // Inserir novos elementos
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
