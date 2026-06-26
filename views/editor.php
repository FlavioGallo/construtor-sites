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
        
        .element .delete-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            display: none;
            font-size: 12px;
            pointer-events: auto;
        }
        .element:hover .delete-btn { display: block; }

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
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 400px;
        }
        .modal-content h3 { margin-bottom: 15px; }
        .modal-content input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .modal-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-cancel { background: #ccc; }
        .btn-confirm { background: #4a90d9; color: white; }
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
    
    <!-- Modal para URL da imagem -->
    <div class="modal" id="imageModal">
        <div class="modal-content">
            <h3>🖼️ Adicionar Imagem</h3>
            <input type="text" id="imageUrl" placeholder="Cole a URL da imagem aqui...">
            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">
                Dica: Use imagens do Imgur, Google Drive (link direto) ou qualquer URL pública
            </p>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeImageModal()">Cancelar</button>
                <button class="btn-confirm" onclick="confirmImage()">Adicionar</button>
            </div>
        </div>
    </div>

    <script>
        let elementIdCounter = 0;
        const PAGE_ID = <?= json_encode($pageId ?? 1) ?>;

        function addElement(type) {
            if (type === 'image') {
                openImageModal();
                return;
            }
            
            createElement(type, type === 'image' ? '' : null);
        }
        
        function openImageModal() {
            document.getElementById('imageModal').style.display = 'flex';
            document.getElementById('imageUrl').value = '';
            document.getElementById('imageUrl').focus();
        }
        
        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }
        
        function confirmImage() {
            const url = document.getElementById('imageUrl').value.trim();
            if (!url) {
                alert('Por favor, digite uma URL!');
                return;
            }
            closeImageModal();
            createElement('image', url);
        }
        
        // Permitir Enter no input
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('imageUrl').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') confirmImage();
            });
        });

        function createElement(type, imageUrl = null) {
            const canvas = document.getElementById('canvas');
            const id = 'el-' + (++elementIdCounter);

            const el = document.createElement('div');
            el.className = 'element';
            el.id = id;
            el.dataset.type = type;

            // Botão de deletar
            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'delete-btn';
            deleteBtn.innerHTML = '✕';
            deleteBtn.onclick = (e) => {
                e.stopPropagation();
                el.remove();
            };
            el.appendChild(deleteBtn);

            if (type === 'text') {
                const p = document.createElement('p');
                p.contentEditable = 'true';
                p.innerText = 'Clique para editar';
                p.style.width = '100%';
                p.style.height = '100%';
                p.style.outline = 'none';
                el.appendChild(p);
                el.style.width = '200px';
                el.style.height = '50px';
            } else if (type === 'image') {
                const img = document.createElement('img');
                img.src = imageUrl || 'https://placehold.co/200x150/4a90d9/white?text=Imagem';
                img.alt = 'imagem';
                img.onerror = () => {
                    img.src = 'https://via.placeholder.com/200x150?text=URL+Inválida';
                };
                el.appendChild(img);
                el.style.width = '200px';
                el.style.height = '150px';
            } else if (type === 'button') {
                const btn = document.createElement('button');
                btn.innerText = 'Meu Botão';
                btn.style.width = '100%';
                btn.style.height = '100%';
                btn.style.padding = '10px';
                btn.style.background = '#4a90d9';
                btn.style.color = 'white';
                btn.style.border = 'none';
                btn.style.borderRadius = '6px';
                btn.style.cursor = 'pointer';
                el.appendChild(btn);
                el.style.width = '150px';
                el.style.height = '45px';
            }

            el.style.left = (100 + Math.random() * 100) + 'px';
            el.style.top = (100 + Math.random() * 100) + 'px';

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
                pageId: PAGE_ID,
                elements: []
            };

            elements.forEach(el => {
                const x = parseFloat(el.dataset.x || 0) + parseFloat(el.style.left || 0);
                const y = parseFloat(el.dataset.y || 0) + parseFloat(el.style.top || 0);
                
                let content = '';
                if (el.dataset.type === 'text') {
                    content = el.querySelector('p')?.innerText || '';
                } else if (el.dataset.type === 'image') {
                    content = el.querySelector('img')?.src || '';
                } else if (el.dataset.type === 'button') {
                    content = el.querySelector('button')?.innerText || '';
                }

                data.elements.push({
                    type: el.dataset.type,
                    content: content,
                    x: Math.round(x),
                    y: Math.round(y),
                    width: el.offsetWidth,
                    height: el.offsetHeight,
                    styles: {}
                });
            });

            if (data.elements.length === 0) {
                alert('⚠️ Adicione pelo menos um elemento antes de salvar!');
                return;
            }

            console.log('Salvando:', data);

            fetch('/api/save-elements', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Status:', response.status);
                return response.json();
            })
            .then(result => {
                console.log('Resposta:', result);
                if (result.success) {
                    alert('✅ Página salva com sucesso!');
                } else {
                    alert('❌ Erro: ' + (result.error || result.message || 'Erro desconhecido'));
                }
            })
            .catch(err => {
                console.error('Erro:', err);
                alert('❌ Erro de conexão: ' + err.message);
            });
        }
    </script>

</body>
</html>
