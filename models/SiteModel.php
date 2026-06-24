<?php
class SiteModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Busca site pelo slug
    public function getBySlug($slug) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM sites WHERE slug = ? AND is_published = 1"
        );
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    // Busca página pelo slug
    public function getPage($siteId, $pageSlug) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM pages WHERE site_id = ? AND slug = ?"
        );
        $stmt->execute([$siteId, $pageSlug]);
        return $stmt->fetch();
    }

    // Busca todos os elementos de uma página
    public function getElements($pageId) {
        $stmt = $this->pdo->prepare("SELECT * FROM elements WHERE page_id = ?");
        $stmt->execute([$pageId]);
        return $stmt->fetchAll();
    }

    // Cria um novo site
    public function createSite($userId, $name, $slug) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO sites (user_id, name, slug) VALUES (?, ?, ?)"
        );
        $stmt->execute([$userId, $name, $slug]);
        $siteId = $this->pdo->lastInsertId();

        // Cria a página "home" automaticamente
        $this->pdo->prepare(
            "INSERT INTO pages (site_id, name, slug) VALUES (?, 'Home', 'home')"
        )->execute([$siteId]);

        return $siteId;
    }

    // Publica o site
    public function publishSite($siteId) {
        $stmt = $this->pdo->prepare(
            "UPDATE sites SET is_published = 1 WHERE id = ?"
        );
        return $stmt->execute([$siteId]);
    }
}