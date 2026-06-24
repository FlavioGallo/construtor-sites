<?php
// Conexão com o MySQL (funciona no Railway e local)

$host = getenv('MYSQLHOST') ?: 'localhost';
$db   = getenv('MYSQLDATABASE') ?: 'construtor_sites';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';
$port = getenv('MYSQLPORT') ?: '3306';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    return $pdo;
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Erro na conexão com o banco: ' . $e->getMessage()]));
}