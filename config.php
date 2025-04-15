<?php
$host = 'localhost';
$db = 'gestion_budget';
$user = 'root';
$pass = '';

try {
    $connection = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}
?>
