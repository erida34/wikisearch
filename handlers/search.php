<?php

define('KEY', true); // ключ безопасности
include '../core/db.php';
$db = new Database(DATAHOST, DBUSER, DBPASSWORD, DATABASE);

$word = $_POST['word']; // обрабатываем запрос
$word = trim($word);
$word = stripslashes($word);
$word = htmlspecialchars($word);

$var_output = $db->search_pages($word);

include 'output.php';