<?php
$host = 'switchyard.proxy.rlwy.net';
$port = '57585';
$db   = 'railway';
$user = 'root';
$pass = 'tWiAuIfuZyuVJJfqkstaCCVIJfRoBxwU';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db",
        $user,
        $pass,
        [
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            PDO::MYSQL_ATTR_SSL_CA => true,
            PDO::ATTR_TIMEOUT => 30,
        ]
    );
    echo "Connected successfully!\n";
} catch (Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}