<?php

define('KEY', true); // ключ безопасности
include '../core/db.php';
$db = new Database(DATAHOST, DBUSER, DBPASSWORD, DATABASE);

$page_id = (int)$_POST['page_id'];

$db->save_page($page_id);