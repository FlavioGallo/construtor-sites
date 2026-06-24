<?php
class PageController {
    private $pageModel;

    public function __construct($pdo) {
        $this->pageModel = new PageModel($pdo);
    }

    // Recebe JSON do editor e salva os elementos
    public function saveElements() {
        header('Content-Type: application/json');

        try {
            $jsonInput = file_get_contents('php://input');
            $data = json_decode($jsonInput, true);

            if (!$data || !isset($data['page_id']) || !isset($data['elements'])) {
                throw new Exception('Dados inválidos');
            }

            $pageId = (int)$data['page_id'];
            $elements = $data['elements'];

            $success = $this->pageModel->updateElements($pageId, $elements);

            echo json_encode([
                'status' => $success ? 'sucesso' : 'erro',
                'message' => $success ? 'Página salva!' : 'Erro ao salvar'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'erro', 'message' => $e->getMessage()]);
        }
    }

    // Carrega o editor
    public function editor($pageId = 1) {
        $elements = $this->pageModel->getElements($pageId);
        include __DIR__ . '/../views/editor.php';
    }
}