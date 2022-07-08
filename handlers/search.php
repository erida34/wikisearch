<?php

define('KEY', true); // ключ безопасности
include 'db.php';

$word = $_POST['word']; // обрабатываем запрос
$word = trim($word);
$word = stripslashes($word);
$word = htmlspecialchars($word);
$sql = 'SELECT * FROM `pages` RIGHT JOIN (SELECT `id_page` FROM `words` WHERE `word`=:plain_text GROUP BY `word`, `id_page` ORDER BY count(`word`)  DESC) as `w` ON `pages`.`pageid`=`w`.`id_page`';
//Подготавливаем PDO выражение для SQL запроса
$stmt = $db->prepare($sql);
$stmt->bindValue(':plain_text', $word, PDO::PARAM_STR);
$stmt->execute(); // запрос на создание записи
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);