<?php
require_once 'config/database.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✅ Tabela 'users' criada!<br>";
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            is_published TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    echo "✅ Tabela 'sites' criada!<br>";
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS pages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            site_id INT NOT NULL,
            name VARCHAR(50) NOT NULL,
            slug VARCHAR(50) NOT NULL,
            FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE CASCADE
        )
    ");
    echo "✅ Tabela 'pages' criada!<br>";
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS elements (
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
        )
    ");
    echo "✅ Tabela 'elements' criada!<br><br>";
    echo "🎉 Banco de dados configurado com sucesso!";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
