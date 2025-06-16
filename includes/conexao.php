<?php
// Define as credenciais para a conexão com o banco de dados.
$host = 'localhost';
$db   = 'catalogo_filmes';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Monta a string DSN para o PDO.
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
// Define opções de conexão: tratamento de erros, modo de busca padrão, etc.
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Tenta estabelecer a conexão com o banco de dados.
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Em caso de falha na conexão, lança uma exceção.
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>