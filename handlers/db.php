<?php
 //Ключ защиты
if(!defined('KEY'))
{
	exit('Нет ключа!');
}

//Логин БД
define('DBUSER', '###');

//Пароль БД
define('DBPASSWORD', '###');

//БД
define('DATABASE', '###');
//Подключение к базе данных mySQL с помощью PDO
try {
	$db = new PDO('mysql:host=###;dbname=' . DATABASE, DBUSER, DBPASSWORD, array(
		PDO::ATTR_PERSISTENT => true
	));
	$sql = 'SET NAMES "UTF8"';
	$stmt = $db->prepare($sql);
	$stmt->execute();
} catch (PDOException $e) {
	print "Ошибка соединеия!: " . $e->getMessage() . "<br/>";
	die();
}
