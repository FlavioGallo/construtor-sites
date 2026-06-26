<?php
class SiteController {
    private $siteModel;

    public function __construct($pdo) {
        $this->siteModel = new SiteModel($pdo);
    }

    // Renderiza o site para o visitante
    public function view($siteSlug, $pageSlug = 'home') {
        $site = $this->siteModel->getBySlug($siteSlug);

        if (!$site) {
            http_response_code(404);
            echo "Site não encontrado ou não publicado.";
            return;
        }

        $page = $this->siteModel->getPage($site['id'], $pageSlug);

        if (!$page) {
            http_response_code(404);
            echo "Página não encontrada.";
            return;
        }

        $elements = $this->siteModel->getElements($page['id']);
        include __DIR__ . '/../views/site_render.php';
    }
}
