<?php

define('KEY', true); // ключ безопасности
include 'db.php';

$sql = 'SELECT *
        FROM `pages`';
//Подготавливаем PDO выражение для SQL запроса
$stmt = $db->prepare($sql);
$stmt->execute(); // запрос на создание записи
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);