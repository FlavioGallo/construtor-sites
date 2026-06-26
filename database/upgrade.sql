-- Tabela de usuários (já existe, mas vamos garantir)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de sites
CREATE TABLE IF NOT EXISTS sites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    template VARCHAR(50) DEFAULT 'blank',
    is_published TINYINT(1) DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabela de páginas
CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    is_home TINYINT(1) DEFAULT 0,
    parent_id INT NULL,
    `order` INT DEFAULT 0,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES pages(id) ON DELETE SET NULL,
    UNIQUE KEY unique_slug (site_id, slug)
);

-- Tabela de elementos
CREATE TABLE IF NOT EXISTS elements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    content TEXT,
    pos_x INT NOT NULL DEFAULT 0,
    pos_y INT NOT NULL DEFAULT 0,
    width INT NOT NULL DEFAULT 100,
    height INT NOT NULL DEFAULT 50,
    styles JSON,
    `order` INT DEFAULT 0,
    parent_element_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_element_id) REFERENCES elements(id) ON DELETE SET NULL
);

-- Tabela de uploads/mídia
CREATE TABLE IF NOT EXISTS media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100),
    file_size INT,
    url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabela de templates
CREATE TABLE IF NOT EXISTS templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    thumbnail VARCHAR(500),
    preview_url VARCHAR(500),
    data JSON,
    is_premium TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserir templates básicos
INSERT INTO templates (name, category, thumbnail, data) VALUES
('Restaurante', 'business', '', '{"pages": []}'),
('Portfolio', 'creative', '', '{"pages": []}'),
('Loja Virtual', 'ecommerce', '', '{"pages": []}'),
('Blog', 'content', '', '{"pages": []}'),
('Landing Page', 'marketing', '', '{"pages": []}');

-- Adicionar coluna de senha temporária se não existir
ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_token VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_token_expires TIMESTAMP NULL;
