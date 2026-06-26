<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/PageController.php';
require_once __DIR__ . '/controllers/SiteController.php';

// Router simples
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

// Rotas de API
if (strpos($uri, 'api/') === 0) {
    header('Content-Type: application/json');
    
    // Auth
    if ($uri === 'api/auth/register' && $method === 'POST') {
        $controller = new AuthController($pdo);
        $controller->register();
        exit;
    }
    
    if ($uri === 'api/auth/login' && $method === 'POST') {
        $controller = new AuthController($pdo);
        $controller->login();
        exit;
    }
    
    if ($uri === 'api/auth/logout' && $method === 'POST') {
        $controller = new AuthController($pdo);
        $controller->logout();
        exit;
    }
    
    if ($uri === 'api/auth/check' && $method === 'GET') {
        $controller = new AuthController($pdo);
        $controller->check();
        exit;
    }
    
    // Sites
    if ($uri === 'api/sites' && $method === 'GET') {
        require_once __DIR__ . '/api/sites.php';
        exit;
    }
    
    if ($uri === 'api/sites' && $method === 'POST') {
        require_once __DIR__ . '/api/sites.php';
        exit;
    }
    
    if (preg_match('/^api\/sites\/(\d+)$/', $uri, $matches) && $method === 'DELETE') {
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/api/sites.php';
        exit;
    }
    
    // Stats
    if ($uri === 'api/stats' && $method === 'GET') {
        require_once __DIR__ . '/api/stats.php';
        exit;
    }
    
    // Media
    if ($uri === 'api/media' && $method === 'GET') {
        require_once __DIR__ . '/api/media.php';
        exit;
    }
    
    if ($uri === 'api/media' && $method === 'POST') {
        require_once __DIR__ . '/api/media.php';
        exit;
    }
    
    if (preg_match('/^api\/media\/(\d+)$/', $uri, $matches) && $method === 'DELETE') {
        $_GET['id'] = $matches[1];
        require_once __DIR__ . '/api/media.php';
        exit;
    }
    
    // Save elements
    if ($uri === 'api/save-elements' && $method === 'POST') {
        $controller = new PageController($pdo);
        $controller->saveElements();
        exit;
    }
    
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint não encontrado']);
    exit;
}

// Rotas de páginas
if ($uri === '' || $uri === 'index.php') {
    include __DIR__ . '/views/landing_page.php';
    exit;
}

if ($uri === 'login') {
    include __DIR__ . '/views/login.php';
    exit;
}

if ($uri === 'register') {
    include __DIR__ . '/views/register.php';
    exit;
}

if ($uri === 'dashboard') {
    include __DIR__ . '/views/dashboard.php';
    exit;
}

if ($uri === 'media-library') {
    include __DIR__ . '/views/media-library.php';
    exit;
}

if ($uri === 'editor' || $uri === 'editor/') {
    $pageId = $_GET['page'] ?? 1;
    $controller = new PageController($pdo);
    $controller->editor($pageId);
    exit;
}

// Rota para visualizar site publicado
$pathParts = explode('/', $uri);
$siteSlug = $pathParts[0] ?? null;
$pageSlug = $pathParts[1] ?? 'home';

if ($siteSlug) {
    $controller = new SiteController($pdo);
    $controller->view($siteSlug, $pageSlug);
    exit;
}

// 404
http_response_code(404);
echo "Página não encontrada.";
