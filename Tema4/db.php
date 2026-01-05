<?php
$host = 'localhost';
$db   = 'sfologea_Sala_Fitness';
$user = 'sfologea_sfologea';
$pass = 'SZ5a#P.3ew4gZ6'; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Eroare la conectare: " . $e->getMessage());
}
?>