<?php
$host = 'localhost';
$dbname = 'ecommerce';
$user = 'root';//default in xxamp
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);//establish connection for database
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>