<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editor de Sites</title>
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { display: flex; height: 100vh; font-family: Arial, sans-serif; }

        .sidebar {
            width: 220px;
            background: #1e1e2e;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .sidebar h3 { margin-bottom: 10px; }
        .sidebar button {
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #3b3b5c;
            color: white;
            cursor: pointer;
            font-size: 14px;
        }
        .sidebar button:hover { background: #5a5a8a; }

        .canvas {
            flex: 1;
            background: #f0f0f0;
            position: relative;
            overflow: hidden;
        }

        .element {
            position: absolute;
            cursor: move;
            border: 2px solid transparent;
            padding: 8px;
        }
        .element:hover, .element.selected {
            border: 2px dashed #4a90d9;
        }
        .element img { width: 100%; height: 100%; object-fit: cover; }
        .element button { pointer-events: none; }

        .save-btn {
            margin-top: auto;
            padding: 14px;
            background: #4CAF50;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .save-btn:hover { background: #45a049; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>➕ Elementos</h3>
        <button onclick="addElement('text')">📝 Texto</button>
        <button onclick="addElement('image')">🖼️ Imagem</button>
        <button onclick="addElement('button')">🔘 Botão</button>
        <button class="save-btn" onclick="savePage()">💾 Salvar Página</button>
    </div>

    <div class="canvas" id="canvas"></div>

    <script>
        let elementIdCounter = 0;
        const PAGE_ID = <?= json_encode($pageId ?? 1) ?>;

        function addElement(type) {
            const canvas = document.getElementById('canvas');
            const id = 'el-' + (++elementIdCounter);

            const el = document.createElement('div');
            el.className = 'element';
            el.id = id;
            el.dataset.type = type;

            if (type === 'text') {
                el.innerHTML = '<p contenteditable="true">Clique para editar</p>';
                el.style.width = '200px';
                el.style.height = '50px';
            } else if (type === 'image') {
                el.innerHTML = '<img src="https://via.placeholder.com/200x150" alt="imagem">';
                el.style.width = '200px';
                el.style.height = '150px';
            } else if (type === 'button') {
                el.innerHTML = '<button>Meu Botão</button>';
                el.style.width = '150px';
                el.style.height = '45px';
            }

            el.style.left = '100px';
            el.style.top = '100px';

            canvas.appendChild(el);
            makeDraggable(el);
        }

        function makeDraggable(el) {
            interact(el)
                .draggable({
                    listeners: {
                        move(event) {
                            const x = (parseFloat(el.dataset.x) || 0) + event.dx;
                            const y = (parseFloat(el.dataset.y) || 0) + event.dy;
                            el.style.transform = `translate(${x}px, ${y}px)`;
                            el.dataset.x = x;
                            el.dataset.y = y;
                        }
                    }
                })
                .resizable({
                    edges: { right: true, bottom: true },
                    listeners: {
                        move(event) {
                            el.style.width = event.rect.width + 'px';
                            el.style.height = event.rect.height + 'px';
                        }
                    }
                });
        }

        function savePage() {
            const elements = document.querySelectorAll('.element');
            const data = {
                page_id: PAGE_ID,
                elements: []
            };

            elements.forEach(el => {
                const x = parseFloat(el.dataset.x || 0) + parseFloat(el.style.left);
                const y = parseFloat(el.dataset.y || 0) + parseFloat(el.style.top);

                data.elements.push({
                    type: el.dataset.type,
                    content: el.innerText || el.querySelector('img')?.src || '',
                    pos_x: Math.round(x),
                    pos_y: Math.round(y),
                    width: el.offsetWidth,
                    height: el.offsetHeight,
                    styles: {}
                });
            });

            fetch('/api/save-elements', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                alert(result.status === 'sucesso' ? '✅ Página salva!' : '❌ ' + result.message);
            })
            .catch(err => alert('❌ Erro: ' + err.message));
        }
    </script>

</body>
</html>