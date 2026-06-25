<?php
// Definir caminho base absoluto
$basePath = '/var/www/html';

// Conexão com o banco
$pdo = require $basePath . '/config/database.php';

// Incluir classes com caminho absoluto
require_once $basePath . '/controllers/PageController.php';
require_once $basePath . '/controllers/SiteController.php';
require_once $basePath . '/models/PageModel.php';
require_once $basePath . '/models/SiteModel.php';

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
    include $basePath . '/views/landing_page.php';
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
