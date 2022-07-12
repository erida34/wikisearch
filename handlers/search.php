<?php

define('KEY', true); // ключ безопасности
include 'db.php';

$word = $_POST['word']; // обрабатываем запрос
$word = trim($word);
$word = stripslashes($word);
$word = htmlspecialchars($word);
$sql = 'SELECT * FROM `pages` RIGHT JOIN (SELECT `id_page`, `count_words` FROM `conn_pages_words` RIGHT JOIN (SELECT `id` FROM `words` WHERE `word`=:word GROUP BY `word`, `id_page`) as `w1` ON `w1`.`id`=`conn_pages_words`.`id_word`) as `w2` ON `w2`.`id_page`=`pages`.`id` ORDER BY `count_words` DESC';
//Подготавливаем PDO выражение для SQL запроса
$stmt = $db->prepare($sql);
$stmt->bindValue(':word', $word, PDO::PARAM_STR);
$stmt->execute(); // запрос на создание записи
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);