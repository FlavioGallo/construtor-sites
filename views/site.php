<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($site['name']) ?></title>
    <?php if ($page['meta_title']): ?>
        <title><?= htmlspecialchars($page['meta_title']) ?></title>
    <?php endif; ?>
    <?php if ($page['meta_description']): ?>
        <meta name="description" content="<?= htmlspecialchars($page['meta_description']) ?>">
    <?php endif; ?>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; overflow-x: hidden; }
        .element { position: absolute; }
        .element img { width: 100%; height: 100%; object-fit: cover; }
        .element button { 
            padding: 10px 20px; 
            cursor: pointer;
            border: none;
            border-radius: 6px;
            background: #4a90d9;
            color: white;
        }
        .video-container { position: relative; padding-bottom: 56.25%; height: 0; }
        .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
    </style>
</head>
<body>
    <?php foreach ($elements as $element): ?>
        <div class="element" style="
            left: <?= $element['pos_x'] ?>px;
            top: <?= $element['pos_y'] ?>px;
            width: <?= $element['width'] ?>px;
            height: <?= $element['height'] ?>px;
            <?= isset($element['styles']['zIndex']) ? 'z-index: ' . $element['styles']['zIndex'] . ';' : '' ?>
        ">
            <?php
            $content = json_decode($element['content'], true) ?? $element['content'];
            
            switch ($element['type']) {
                case 'text':
                    echo '<div style="' . ($element['styles']['text'] ?? '') . '">' . htmlspecialchars($content) . '</div>';
                    break;
                    
                case 'image':
                    echo '<img src="' . htmlspecialchars($content) . '" alt="Imagem">';
                    break;
                    
                case 'button':
                    $url = $element['styles']['link'] ?? '#';
                    echo '<a href="' . htmlspecialchars($url) . '" style="text-decoration: none;">
                            <button>' . htmlspecialchars($content) . '</button>
                          </a>';
                    break;
                    
                case 'video':
                    $videoId = getYoutubeId($content);
                    echo '<div class="video-container">
                            <iframe src="https://www.youtube.com/embed/' . $videoId . '" 
                                    frameborder="0" allowfullscreen></iframe>
                          </div>';
                    break;
                    
                case 'form':
                    include __DIR__ . '/partials/contact_form.php';
                    break;
            }
            ?>
        </div>
    <?php endforeach; ?>
</body>
</html>

<?php
function getYoutubeId($url) {
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $url, $matches);
    return $matches[1] ?? '';
}
?>
