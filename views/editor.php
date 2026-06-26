<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Sites Profissional</title>
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { display: flex; height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow: hidden; }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: #1e1e2e;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow-y: auto;
        }
        .sidebar h3 { 
            margin-bottom: 10px;
            font-size: 16px;
            color: #a0a0b0;
        }
        .sidebar button {
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #3b3b5c;
            color: white;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }
        .sidebar button:hover { 
            background: #5a5a8a;
            transform: translateX(5px);
        }
        .sidebar button i {
            width: 20px;
            text-align: center;
        }

        /* Canvas */
        .canvas {
            flex: 1;
            background: #f0f0f0;
            position: relative;
            overflow: hidden;
        }

        /* Elementos */
        .element {
            position: absolute;
            cursor: move;
            border: 2px solid transparent;
            padding: 8px;
            transition: border-color 0.2s;
        }
        .element:hover, .element.selected {
            border: 2px dashed #4a90d9;
        }
        .element img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover;
            pointer-events: none;
        }
        .element button { pointer-events: none; }
        .element .delete-btn {
            position: absolute;
            top: -12px;
            right: -12px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            cursor: pointer;
            display: none;
            font-size: 14px;
            pointer-events: auto;
            z-index: 100;
        }
        .element:hover .delete-btn { display: block; }
        
        /* Botão Salvar */
        .save-btn {
            margin-top: auto;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .save-btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
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
            width: 100%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .modal-header h3 { 
            color: #333;
            font-size: 20px;
        }
        .close-modal {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }
        .modal-content input,
        .modal-content textarea,
        .modal-content select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .modal-content input:focus,
        .modal-content textarea:focus,
        .modal-content select:focus {
            outline: none;
            border-color: #667eea;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .modal-buttons button {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-cancel { background: #e0e0e0; color: #333; }
        .btn-confirm { background: #667eea; color: white; }
        
        /* Toolbar */
        .toolbar {
            position: absolute;
            top: 20px;
            right: 20px;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            gap: 10px;
        }
        .toolbar button {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 6px;
            background: #f0f0f0;
            cursor: pointer;
            font-size: 16px;
        }
        .toolbar button:hover {
            background: #667eea;
            color: white;
        }
        
        /* Media Library Modal */
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            max-height: 400px;
            overflow-y: auto;
        }
        .media-item {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s;
        }
        .media-item:hover {
            border-color: #667eea;
            transform: scale(1.05);
        }
        .media-item img {
            width: 100%;
            height: 100px;
            object-fit: cover;
        }
        .media-item p {
            padding: 8px;
            font-size: 12px;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>➕ Elementos</h3>
        <button onclick="addElement('text')">
            <i class="fas fa-font"></i> Texto
        </button>
        <button onclick="addElement('image')">
            <i class="fas fa-image"></i> Imagem
        </button>
        <button onclick="addElement('button')">
            <i class="fas fa-circle"></i> Botão
        </button>
        <button onclick="addElement('video')">
            <i class="fas fa-video"></i> Vídeo
        </button>
        <button onclick="addElement('form')">
            <i class="fas fa-envelope"></i> Formulário
        </button>
        <button onclick="addElement('map')">
            <i class="fas fa-map-marker-alt"></i> Mapa
        </button>
        <button onclick="addElement('social')">
            <i class="fas fa-share-alt"></i> Redes Sociais
        </button>
        <button onclick="addElement('divider')">
            <i class="fas fa-minus"></i> Separador
        </button>
        
        <button class="save-btn" onclick="savePage()">
            <i class="fas fa-save"></i> Salvar Página
        </button>
    </div>

    <div class="canvas" id="canvas"></div>
    
    <!-- Toolbar -->
    <div class="toolbar">
        <button onclick="undo()" title="Desfazer"><i class="fas fa-undo"></i></button>
        <button onclick="redo()" title="Refazer"><i class="fas fa-redo"></i></button>
        <button onclick="clearCanvas()" title="Limpar tudo"><i class="fas fa-trash"></i></button>
        <button onclick="previewSite()" title="Preview"><i class="fas fa-eye"></i></button>
    </div>
    
    <!-- Modal Imagem -->
    <div class="modal" id="imageModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>🖼️ Adicionar Imagem</h3>
                <button class="close-modal" onclick="closeModal('imageModal')">&times;</button>
            </div>
            <input type="text" id="imageUrl" placeholder="Cole a URL da imagem...">
            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">
                Ou escolha da biblioteca de mídia:
            </p>
            <button class="btn-confirm" style="width: 100%; margin-bottom: 15px;" onclick="openMediaLibrary()">
                <i class="fas fa-folder-open"></i> Abrir Biblioteca de Mídia
            </button>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeModal('imageModal')">Cancelar</button>
                <button class="btn-confirm" onclick="confirmImage()">Adicionar</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Vídeo -->
    <div class="modal" id="videoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>🎥 Adicionar Vídeo</h3>
                <button class="close-modal" onclick="closeModal('videoModal')">&times;</button>
            </div>
            <input type="text" id="videoUrl" placeholder="URL do YouTube ou Vimeo...">
            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">
                Ex: https://www.youtube.com/watch?v=...
            </p>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeModal('videoModal')">Cancelar</button>
                <button class="btn-confirm" onclick="confirmVideo()">Adicionar</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Formulário -->
    <div class="modal" id="formModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📧 Configurar Formulário</h3>
                <button class="close-modal" onclick="closeModal('formModal')">&times;</button>
            </div>
            <label>Email de destino:</label>
            <input type="email" id="formEmail" placeholder="seu@email.com">
            <label>Título:</label>
            <input type="text" id="formTitle" placeholder="Entre em contato">
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeModal('formModal')">Cancelar</button>
                <button class="btn-confirm" onclick="confirmForm()">Adicionar</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Mapa -->
    <div class="modal" id="mapModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>️ Adicionar Mapa</h3>
                <button class="close-modal" onclick="closeModal('mapModal')">&times;</button>
            </div>
            <input type="text" id="mapAddress" placeholder="Endereço ou coordenadas...">
            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">
                Ex: São Paulo, Brasil ou -23.5505,-46.6333
            </p>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeModal('mapModal')">Cancelar</button>
                <button class="btn-confirm" onclick="confirmMap()">Adicionar</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Redes Sociais -->
    <div class="modal" id="socialModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3> Redes Sociais</h3>
                <button class="close-modal" onclick="closeModal('socialModal')">&times;</button>
            </div>
            <label>Facebook:</label>
            <input type="text" id="socialFacebook" placeholder="https://facebook.com/...">
            <label>Instagram:</label>
            <input type="text" id="socialInstagram" placeholder="https://instagram.com/...">
            <label>Twitter:</label>
            <input type="text" id="socialTwitter" placeholder="https://twitter.com/...">
            <label>LinkedIn:</label>
            <input type="text" id="socialLinkedin" placeholder="https://linkedin.com/...">
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeModal('socialModal')">Cancelar</button>
                <button class="btn-confirm" onclick="confirmSocial()">Adicionar</button>
            </div>
        </div>
    </div>

    <script>
        let elementIdCounter = 0;
        const PAGE_ID = <?= json_encode($pageId ?? 1) ?>;
        let undoStack = [];
        let redoStack = [];

        function addElement(type) {
            switch(type) {
                case 'image':
                    openModal('imageModal');
                    break;
                case 'video':
                    openModal('videoModal');
                    break;
                case 'form':
                    openModal('formModal');
                    break;
                case 'map':
                    openModal('mapModal');
                    break;
                case 'social':
                    openModal('socialModal');
                    break;
                default:
                    createElement(type);
            }
        }
        
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }
        
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        
        function createElement(type, data = {}) {
            saveState();
            
            const canvas = document.getElementById('canvas');
            const id = 'el-' + (++elementIdCounter);

            const el = document.createElement('div');
            el.className = 'element';
            el.id = id;
            el.dataset.type = type;

            // Botão de deletar
            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'delete-btn';
            deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
            deleteBtn.onclick = (e) => {
                e.stopPropagation();
                saveState();
                el.remove();
            };
            el.appendChild(deleteBtn);

            switch(type) {
                case 'text':
                    const p = document.createElement('p');
                    p.contentEditable = 'true';
                    p.innerText = data.content || 'Clique para editar';
                    p.style.cssText = 'width: 100%; height: 100%; outline: none; font-size: 16px;';
                    el.appendChild(p);
                    el.style.width = (data.width || 200) + 'px';
                    el.style.height = (data.height || 50) + 'px';
                    break;
                    
               case 'image':
    const img = document.createElement('img');
    const imageUrl = data.content || 'https://placehold.co/200x150/4a90d9/white?text=Imagem';
    img.src = imageUrl;
    img.alt = 'imagem';
    img.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
    img.onerror = () => {
        // Manter a URL original mesmo se falhar
        img.style.display = 'none';
        const placeholder = document.createElement('div');
        placeholder.style.cssText = 'width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; text-align: center; padding: 10px;';
        placeholder.innerHTML = '<i class="fas fa-image" style="font-size: 32px; margin-bottom: 10px; display: block;"></i>Imagem<br><small style="font-size: 10px;">Clique para editar URL</small>';
        placeholder.onclick = () => {
            const newUrl = prompt('Cole a URL da imagem:', imageUrl);
            if (newUrl) {
                img.src = newUrl;
                img.style.display = 'block';
                placeholder.remove();
            }
        };
        el.appendChild(placeholder);
    };
    el.appendChild(img);
    el.style.width = (data.width || 200) + 'px';
    el.style.height = (data.height || 150) + 'px';
    break;
                    
                case 'button':
                    const btn = document.createElement('button');
                    btn.innerText = data.content || 'Meu Botão';
                    btn.style.cssText = 'width: 100%; height: 100%; padding: 10px; background: #4a90d9; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;';
                    el.appendChild(btn);
                    el.style.width = (data.width || 150) + 'px';
                    el.style.height = (data.height || 45) + 'px';
                    break;
                    
                case 'video':
                    const videoUrl = data.content || '';
                    const videoId = extractVideoId(videoUrl);
                    const videoContainer = document.createElement('div');
                    videoContainer.style.cssText = 'width: 100%; height: 100%; background: #000;';
                    if (videoId) {
                        videoContainer.innerHTML = `<iframe src="https://www.youtube.com/embed/${videoId}" style="width: 100%; height: 100%; border: none;" allowfullscreen></iframe>`;
                    } else {
                        videoContainer.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: white;"><i class="fas fa-video" style="font-size: 48px;"></i></div>';
                    }
                    el.appendChild(videoContainer);
                    el.style.width = (data.width || 400) + 'px';
                    el.style.height = (data.height || 225) + 'px';
                    break;
                    
                case 'form':
                    const formContainer = document.createElement('div');
                    formContainer.style.cssText = 'width: 100%; height: 100%; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);';
                    formContainer.innerHTML = `
                        <h4 style="margin-bottom: 15px; color: #333;">${data.title || 'Entre em contato'}</h4>
                        <input type="text" placeholder="Nome" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <input type="email" placeholder="Email" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <textarea placeholder="Mensagem" style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; height: 80px;"></textarea>
                        <button style="width: 100%; padding: 12px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer;">Enviar</button>
                    `;
                    el.appendChild(formContainer);
                    el.style.width = (data.width || 350) + 'px';
                    el.style.height = (data.height || 280) + 'px';
                    break;
                    
                case 'map':
                    const mapContainer = document.createElement('div');
                    mapContainer.style.cssText = 'width: 100%; height: 100%; background: #e0e0e0;';
                    const address = data.content || '';
                    if (address) {
                        mapContainer.innerHTML = `<iframe src="https://maps.google.com/maps?q=${encodeURIComponent(address)}&output=embed" style="width: 100%; height: 100%; border: none;"></iframe>`;
                    } else {
                        mapContainer.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666;"><i class="fas fa-map-marker-alt" style="font-size: 48px;"></i></div>';
                    }
                    el.appendChild(mapContainer);
                    el.style.width = (data.width || 400) + 'px';
                    el.style.height = (data.height || 300) + 'px';
                    break;
                    
                case 'social':
                    const socialContainer = document.createElement('div');
                    socialContainer.style.cssText = 'width: 100%; height: 100%; display: flex; gap: 15px; align-items: center; justify-content: center;';
                    const icons = {
                        facebook: data.facebook || '#',
                        instagram: data.instagram || '#',
                        twitter: data.twitter || '#',
                        linkedin: data.linkedin || '#'
                    };
                    Object.entries(icons).forEach(([network, url]) => {
                        if (url && url !== '#') {
                            const link = document.createElement('a');
                            link.href = url;
                            link.target = '_blank';
                            link.innerHTML = `<i class="fab fa-${network}" style="font-size: 32px; color: #667eea;"></i>`;
                            socialContainer.appendChild(link);
                        }
                    });
                    el.appendChild(socialContainer);
                    el.style.width = (data.width || 200) + 'px';
                    el.style.height = (data.height || 50) + 'px';
                    break;
                    
                case 'divider':
                    const divider = document.createElement('hr');
                    divider.style.cssText = 'width: 100%; border: none; border-top: 2px solid #667eea; margin: 0;';
                    el.appendChild(divider);
                    el.style.width = (data.width || 300) + 'px';
                    el.style.height = (data.height || 10) + 'px';
                    break;
            }

            el.style.left = (data.x || 100 + Math.random() * 100) + 'px';
            el.style.top = (data.y || 100 + Math.random() * 100) + 'px';

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

        function extractVideoId(url) {
            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            const match = url.match(regExp);
            return (match && match[2].length === 11) ? match[2] : null;
        }
        
        function confirmImage() {
            const url = document.getElementById('imageUrl').value.trim();
            closeModal('imageModal');
            if (url) {
                createElement('image', { content: url });
            }
        }
        
        function confirmVideo() {
            const url = document.getElementById('videoUrl').value.trim();
            closeModal('videoModal');
            if (url) {
                createElement('video', { content: url });
            }
        }
        
        function confirmForm() {
            const email = document.getElementById('formEmail').value.trim();
            const title = document.getElementById('formTitle').value.trim();
            closeModal('formModal');
            createElement('form', { email, title: title || 'Entre em contato' });
        }
        
        function confirmMap() {
            const address = document.getElementById('mapAddress').value.trim();
            closeModal('mapModal');
            if (address) {
                createElement('map', { content: address });
            }
        }
        
        function confirmSocial() {
            const data = {
                facebook: document.getElementById('socialFacebook').value.trim(),
                instagram: document.getElementById('socialInstagram').value.trim(),
                twitter: document.getElementById('socialTwitter').value.trim(),
                linkedin: document.getElementById('socialLinkedin').value.trim()
            };
            closeModal('socialModal');
            createElement('social', data);
        }
        
        function openMediaLibrary() {
            window.open('/media-library', '_blank');
        }

        function saveState() {
            const canvas = document.getElementById('canvas');
            undoStack.push(canvas.innerHTML);
            redoStack = [];
        }
        
        function undo() {
            if (undoStack.length === 0) return;
            const canvas = document.getElementById('canvas');
            redoStack.push(canvas.innerHTML);
            canvas.innerHTML = undoStack.pop();
        }
        
        function redo() {
            if (redoStack.length === 0) return;
            const canvas = document.getElementById('canvas');
            undoStack.push(canvas.innerHTML);
            canvas.innerHTML = redoStack.pop();
        }
        
        function clearCanvas() {
            if (!confirm('Tem certeza que deseja limpar tudo?')) return;
            saveState();
            document.getElementById('canvas').innerHTML = '';
        }
        
        function previewSite() {
            const newWindow = window.open('', '_blank');
            const canvas = document.getElementById('canvas');
            newWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head><title>Preview</title></head>
                <body style="margin: 0; padding: 20px; background: #f0f0f0;">
                    ${canvas.innerHTML}
                </body>
                </html>
            `);
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
                } else if (el.dataset.type === 'video') {
                    content = el.dataset.videoUrl || '';
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

            fetch('/api/save-elements', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('✅ Página salva com sucesso!');
                } else {
                    alert('❌ Erro: ' + (result.error || 'Erro desconhecido'));
                }
            })
            .catch(err => {
                alert('❌ Erro de conexão: ' + err.message);
            });
        }
    </script>

</body>
</html>
