CREATE DATABASE IF NOT EXISTS construtor_sites;
USE construtor_sites;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    is_published TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL,
    FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE CASCADE
);

CREATE TABLE elements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    content TEXT,
    pos_x INT NOT NULL,
    pos_y INT NOT NULL,
    width INT NOT NULL,
    height INT NOT NULL,
    styles JSON,
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
);

-- Dados de teste (usuário + site + página)
INSERT INTO users (name, email, password) VALUES ('Admin', 'admin@test.com', '123456');
INSERT INTO sites (user_id, name, slug, is_published) VALUES (1, 'Meu Primeiro Site', 'meusite', 1);
INSERT INTO pages (site_id, name, slug) VALUES (1, 'Home', 'home');