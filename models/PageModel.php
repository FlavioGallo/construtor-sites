<?php
class PageModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Salva/atualiza os elementos de uma página
    public function updateElements($pageId, $elements) {
        try {
            $this->pdo->beginTransaction();

            // Remove elementos antigos
            $this->pdo->prepare("DELETE FROM elements WHERE page_id = ?")
                      ->execute([$pageId]);

            // Insere os novos elementos
            $stmt = $this->pdo->prepare(
                "INSERT INTO elements (page_id, type, content, pos_x, pos_y, width, height, styles) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            foreach ($elements as $el) {
                $stmt->execute([
                    $pageId,
                    $el['type'] ?? 'text',
                    $el['content'] ?? '',
                    (int)($el['pos_x'] ?? 0),
                    (int)($el['pos_y'] ?? 0),
                    (int)($el['width'] ?? 100),
                    (int)($el['height'] ?? 50),
                    json_encode($el['styles'] ?? [])
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    // Busca todos os elementos de uma página
    public function getElements($pageId) {
        $stmt = $this->pdo->prepare("SELECT * FROM elements WHERE page_id = ?");
        $stmt->execute([$pageId]);
        return $stmt->fetchAll();
    }
}