<?php
 //Ключ защиты
if(!defined('KEY'))
{
	exit('Нет ключа!');
}

//Логин БД
define('DBUSER', 'u227548_vlad');

//Пароль БД
define('DBPASSWORD', 'Ytljujkjdjkjvrf');

//БД
define('DATABASE', 'b227548_wikisearch');
//Подключение к базе данных mySQL с помощью PDO
try {
	$db = new PDO('mysql:host=78.108.80.33;dbname=' . DATABASE, DBUSER, DBPASSWORD, array(
		PDO::ATTR_PERSISTENT => true
	));
	$sql = 'SET NAMES "UTF8"';
	$stmt = $db->prepare($sql);
	$stmt->execute();
} catch (PDOException $e) {
	print "Ошибка соединеия!: " . $e->getMessage() . "<br/>";
	die();
}
