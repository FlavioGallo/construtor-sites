<?php
// Definir caminho base
define('BASE_PATH', dirname(__DIR__));

// Conexão com o banco
$pdo = require __DIR__ . '/config/database.php';

// Incluir classes
require_once __DIR__ . '/controllers/PageController.php';
require_once __DIR__ . '/controllers/SiteController.php';
require_once __DIR__ . '/models/PageModel.php';
require_once __DIR__ . '/models/SiteModel.php';

// Pega a URL
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$host = $_SERVER['HTTP_HOST'];

// 🎯 Rota: API para salvar elementos
if ($uri === 'api/save-elements') {
    $controller = new PageController($pdo);
    $controller->saveElements();
    exit;
}

// 🎯 Rota: Editor
if ($uri === 'editor' || $uri === 'editor/') {
    $controller = new PageController($pdo);
    $controller->editor(1);
    exit;
}

// 🎯 Rota: Página inicial da plataforma
if ($uri === '' || $uri === 'index.php') {
    include __DIR__ . '/views/landing_page.php';
    exit;
}

// 🎯 Rota: Renderizar site publicado
$pathParts = explode('/', $uri);
$siteSlug = $pathParts[0] ?? null;
$pageSlug = $pathParts[1] ?? 'home';

if ($siteSlug) {
    $hostParts = explode('.', $host);
    if (count($hostParts) >= 3 && $hostParts[0] !== 'www') {
        $siteSlug = $hostParts[0];
        $pageSlug = $pathParts[0] ?: 'home';
    }

    $controller = new SiteController($pdo);
    $controller->view($siteSlug, $pageSlug);
    exit;
}

// Fallback
http_response_code(404);
echo "Página não encontrada.";
