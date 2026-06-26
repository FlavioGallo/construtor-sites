<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca de Mídia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        
        .header {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .section h2 {
            color: #333;
            font-size: 24px;
        }
        .btn-primary {
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        
        .upload-area {
            border: 3px dashed #ddd;
            border-radius: 12px;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 30px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .upload-area:hover,
        .upload-area.dragover {
            border-color: #667eea;
            background: #f0f3ff;
        }
        .upload-area i {
            font-size: 64px;
            color: #667eea;
            margin-bottom: 15px;
        }
        .upload-area h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .upload-area p {
            color: #666;
        }
        #fileInput {
            display: none;
        }
        
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        .media-item {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            transition: all 0.3s;
        }
        .media-item:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .media-item img,
        .media-item .file-icon {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .media-item .file-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f3ff;
            font-size: 48px;
            color: #667eea;
        }
        .media-info {
            padding: 12px;
            background: white;
        }
        .media-info p {
            font-size: 13px;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .media-info small {
            color: #999;
            font-size: 11px;
        }
        .media-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 5px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .media-item:hover .media-actions {
            opacity: 1;
        }
        .media-actions button {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }
        .btn-copy {
            background: #3498db;
        }
        .btn-delete {
            background: #e74c3c;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🖼️ Biblioteca de Mídia</div>
        <a href="/dashboard" style="color: #667eea; text-decoration: none;">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <div class="container">
        <div class="section">
            <div class="section-header">
                <h2>Meus Arquivos</h2>
                <button class="btn-primary" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-upload"></i> Upload
                </button>
            </div>
            
            <input type="file" id="fileInput" multiple accept="image/*,.pdf">
            
            <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <h3>Arraste arquivos aqui ou clique para selecionar</h3>
                <p>Imagens (JPG, PNG, GIF, WebP) ou PDF até 10MB</p>
            </div>
            
            <div class="media-grid" id="mediaGrid">
                <!-- Arquivos serão carregados aqui -->
            </div>
        </div>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const mediaGrid = document.getElementById('mediaGrid');
        
        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });
        
        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });
        
        async function handleFiles(files) {
            for (let file of files) {
                await uploadFile(file);
            }
            loadMedia();
        }
        
        async function uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            
            try {
                const response = await fetch('/api/media', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('✅ Arquivo enviado com sucesso!');
                } else {
                    alert('❌ Erro: ' + result.error);
                }
            } catch (error) {
                alert('❌ Erro ao enviar arquivo: ' + error.message);
            }
        }
        
        async function loadMedia() {
            try {
                const response = await fetch('/api/media');
                const media = await response.json();
                
                mediaGrid.innerHTML = '';
                
                if (media.length === 0) {
                    mediaGrid.innerHTML = `
                        <div class="empty-state" style="grid-column: 1/-1;">
                            <i class="fas fa-folder-open"></i>
                            <h3>Nenhum arquivo enviado</h3>
                            <p>Faça upload de imagens e arquivos para usar no seu site</p>
                        </div>
                    `;
                    return;
                }
                
                media.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'media-item';
                    
                    const isImage = item.mime_type.startsWith('image/');
                    
                    div.innerHTML = `
                        ${isImage 
                            ? `<img src="${item.url}" alt="${item.original_name}">`
                            : `<div class="file-icon"><i class="fas fa-file"></i></div>`
                        }
                        <div class="media-info">
                            <p>${item.original_name}</p>
                            <small>${formatBytes(item.file_size)}</small>
                        </div>
                        <div class="media-actions">
                            <button class="btn-copy" onclick="copyUrl('${item.url}')" title="Copiar URL">
                                <i class="fas fa-link"></i>
                            </button>
                            <button class="btn-delete" onclick="deleteMedia(${item.id})" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                    
                    div.addEventListener('click', (e) => {
                        if (!e.target.closest('.media-actions')) {
                            copyUrl(item.url);
                        }
                    });
                    
                    mediaGrid.appendChild(div);
                });
            } catch (error) {
                console.error('Erro ao carregar mídia:', error);
            }
        }
        
        function copyUrl(url) {
            const fullUrl = window.location.origin + url;
            navigator.clipboard.writeText(fullUrl).then(() => {
                showNotification('✅ URL copiada!');
            });
        }
        
        async function deleteMedia(id) {
            if (!confirm('Tem certeza que deseja excluir este arquivo?')) return;
            
            try {
                const response = await fetch('/api/media/' + id, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('✅ Arquivo excluído!');
                    loadMedia();
                } else {
                    alert('❌ ' + result.error);
                }
            } catch (error) {
                alert('❌ Erro ao excluir arquivo: ' + error.message);
            }
        }
        
        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
        
        function showNotification(message) {
            const div = document.createElement('div');
            div.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #27ae60;
                color: white;
                padding: 15px 25px;
                border-radius: 8px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.2);
                z-index: 10000;
                animation: slideIn 0.3s ease;
            `;
            div.textContent = message;
            document.body.appendChild(div);
            
            setTimeout(() => {
                div.remove();
            }, 3000);
        }
        
        // Carregar ao iniciar
        loadMedia();
    </script>
</body>
</html>
