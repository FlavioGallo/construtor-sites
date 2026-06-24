<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($site['name']) ?></title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; }
        .page-container {
            position: relative;
            width: 100%;
            min-height: 100vh;
            background: white;
        }
        .element {
            position: absolute;
        }
        .element img { width: 100%; height: 100%; object-fit: cover; }
        .element button {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            background: #4a90d9;
            color: white;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <?php foreach ($elements as $el): ?>
            <?php
            $styles = json_decode($el['styles'], true) ?: [];
            $styleString = "left: {$el['pos_x']}px; top: {$el['pos_y']}px; width: {$el['width']}px; height: {$el['height']}px;";
            
            foreach ($styles as $prop => $value) {
                $styleString .= " $prop: $value;";
            }
            ?>
            
            <div class="element" style="<?= htmlspecialchars($styleString) ?>">
                <?php if ($el['type'] === 'text'): ?>
                    <p><?= nl2br(htmlspecialchars($el['content'])) ?></p>
                <?php elseif ($el['type'] === 'image'): ?>
                    <img src="<?= htmlspecialchars($el['content']) ?>" alt="imagem">
                <?php elseif ($el['type'] === 'button'): ?>
                    <button><?= htmlspecialchars($el['content']) ?></button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>