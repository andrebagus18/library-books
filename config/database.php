<?php
$host = 'localhost';
$port = '5432';
$dbname = 'library_db';
$user = 'postgres';
$password = 'admin123';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function query($sql, $params = [])
{
    global $pdo;
    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    return $statement;
}

function fetchAll($sql, $params = [])
{
    return query($sql,  $params)->fetchAll(PDO::FETCH_ASSOC);
}

function fetchOne($sql, $params = [])
{
    return query($sql,  $params)->fetch(PDO::FETCH_ASSOC);
}
