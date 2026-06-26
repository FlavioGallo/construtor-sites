<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Construtor de Sites</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        
        /* Header */
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
        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .user-name {
            color: #333;
            font-weight: 500;
        }
        .btn-logout {
            padding: 8px 20px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        
        /* Container */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        /* Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
        
        /* Sites Section */
        .section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
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
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        /* Sites Grid */
        .sites-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .site-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
        }
        .site-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .site-thumbnail {
            height: 180px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }
        .site-info {
            padding: 20px;
        }
        .site-info h3 {
            color: #333;
            margin-bottom: 8px;
        }
        .site-info p {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .site-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }
        .btn-edit {
            background: #667eea;
            color: white;
        }
        .btn-view {
            background: #27ae60;
            color: white;
        }
        .btn-delete {
            background: #e74c3c;
            color: white;
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
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .modal-header h2 {
            color: #333;
        }
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        .templates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .template-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .template-card:hover,
        .template-card.selected {
            border-color: #667eea;
            background: #f0f3ff;
        }
        .template-card i {
            font-size: 40px;
            color: #667eea;
            margin-bottom: 10px;
        }
        .template-card span {
            display: block;
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🚀 Construtor de Sites</div>
        <div class="user-menu">
            <span class="user-name" id="userName">Carregando...</span>
            <button class="btn-logout" onclick="logout()">Sair</button>
        </div>
    </div>
    
    <div class="container">
        <!-- Stats -->
        <div class="stats">
            <div class="stat-card">
                <h3>📁 Total de Sites</h3>
                <div class="number" id="totalSites">0</div>
            </div>
            <div class="stat-card">
                <h3>📄 Páginas Criadas</h3>
                <div class="number" id="totalPages">0</div>
            </div>
            <div class="stat-card">
                <h3>🖼️ Arquivos na Mídia</h3>
                <div class="number" id="totalMedia">0</div>
            </div>
        </div>
        
        <!-- Meus Sites -->
        <div class="section">
            <div class="section-header">
                <h2>Meus Sites</h2>
                <button class="btn-primary" onclick="openNewSiteModal()">
                    <i class="fas fa-plus"></i> Novo Site
                </button>
            </div>
            
            <div class="sites-grid" id="sitesGrid">
                <!-- Sites serão carregados aqui -->
            </div>
        </div>
    </div>
    
    <!-- Modal Novo Site -->
    <div class="modal" id="newSiteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Criar Novo Site</h2>
                <button class="close-modal" onclick="closeNewSiteModal()">&times;</button>
            </div>
            
            <form id="newSiteForm">
                <div class="form-group">
                    <label for="siteName">Nome do Site</label>
                    <input type="text" id="siteName" required placeholder="Ex: Meu Site Institucional">
                </div>
                
                <div class="form-group">
                    <label for="siteSlug">URL (slug)</label>
                    <input type="text" id="siteSlug" required placeholder="ex: meu-site">
                    <small style="color: #666;">URL: construtor-sites-production.up.railway.app/<strong>meu-site</strong></small>
                </div>
                
                <div class="form-group">
                    <label>Escolha um Template (opcional)</label>
                    <div class="templates-grid">
                        <div class="template-card selected" data-template="blank">
                            <i class="fas fa-file"></i>
                            <span>Em branco</span>
                        </div>
                        <div class="template-card" data-template="restaurant">
                            <i class="fas fa-utensils"></i>
                            <span>Restaurante</span>
                        </div>
                        <div class="template-card" data-template="portfolio">
                            <i class="fas fa-briefcase"></i>
                            <span>Portfolio</span>
                        </div>
                        <div class="template-card" data-template="store">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Loja Virtual</span>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%;">
                    <i class="fas fa-plus"></i> Criar Site
                </button>
            </form>
        </div>
    </div>

    <script>
        let currentUser = null;
        let selectedTemplate = 'blank';
        
        // Verificar autenticação
        async function checkAuth() {
            try {
                const response = await fetch('/api/auth/check');
                const data = await response.json();
                
                if (!data.authenticated) {
                    window.location.href = '/login';
                    return;
                }
                
                currentUser = data.user;
                document.getElementById('userName').textContent = currentUser.name;
                loadDashboard();
            } catch (error) {
                console.error('Erro de autenticação:', error);
                window.location.href = '/login';
            }
        }
        
        // Carregar dashboard
        async function loadDashboard() {
            await loadSites();
            await loadStats();
        }
        
        // Carregar sites
        async function loadSites() {
            try {
                const response = await fetch('/api/sites');
                const sites = await response.json();
                
                const grid = document.getElementById('sitesGrid');
                grid.innerHTML = '';
                
                if (sites.length === 0) {
                    grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #666; padding: 40px;">Nenhum site criado ainda. Clique em "Novo Site" para começar!</p>';
                    return;
                }
                
                sites.forEach(site => {
                    const card = document.createElement('div');
                    card.className = 'site-card';
                    card.innerHTML = `
                        <div class="site-thumbnail">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="site-info">
                            <h3>${site.name}</h3>
                            <p>${site.is_published ? '✅ Publicado' : '❌ Não publicado'}</p>
                            <div class="site-actions">
                                <button class="btn btn-edit" onclick="editSite(${site.id})">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn btn-view" onclick="viewSite('${site.slug}')">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button class="btn btn-delete" onclick="deleteSite(${site.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    grid.appendChild(card);
                });
            } catch (error) {
                console.error('Erro ao carregar sites:', error);
            }
        }
        
        // Carregar estatísticas
        async function loadStats() {
            try {
                const response = await fetch('/api/stats');
                const stats = await response.json();
                
                document.getElementById('totalSites').textContent = stats.sites || 0;
                document.getElementById('totalPages').textContent = stats.pages || 0;
                document.getElementById('totalMedia').textContent = stats.media || 0;
            } catch (error) {
                console.error('Erro ao carregar stats:', error);
            }
        }
        
        // Modal
        function openNewSiteModal() {
            document.getElementById('newSiteModal').style.display = 'flex';
        }
        
        function closeNewSiteModal() {
            document.getElementById('newSiteModal').style.display = 'none';
            document.getElementById('newSiteForm').reset();
        }
        
        // Selecionar template
        document.querySelectorAll('.template-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.template-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                selectedTemplate = this.dataset.template;
            });
        });
        
        // Criar site
        document.getElementById('newSiteForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = {
                name: document.getElementById('siteName').value,
                slug: document.getElementById('siteSlug').value,
                template: selectedTemplate
            };
            
            try {
                const response = await fetch('/api/sites', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('✅ Site criado com sucesso!');
                    closeNewSiteModal();
                    loadDashboard();
                } else {
                    alert('❌ ' + result.error);
                }
            } catch (error) {
                alert('❌ Erro ao criar site: ' + error.message);
            }
        });
        
        // Ações
        function editSite(siteId) {
            window.location.href = '/editor?site=' + siteId;
        }
        
        function viewSite(slug) {
            window.location.href = '/' + slug;
        }
        
        async function deleteSite(siteId) {
            if (!confirm('Tem certeza que deseja excluir este site?')) return;
            
            try {
                const response = await fetch('/api/sites/' + siteId, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('✅ Site excluído!');
                    loadDashboard();
                } else {
                    alert('❌ ' + result.error);
                }
            } catch (error) {
                alert('❌ Erro ao excluir site: ' + error.message);
            }
        }
        
        function logout() {
            fetch('/api/auth/logout').then(() => {
                window.location.href = '/login';
            });
        }
        
        // Inicializar
        checkAuth();
    </script>
</body>
</html>
