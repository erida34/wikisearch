<?php

define('KEY', true); // ключ безопасности
include '../core/db.php';
$db = new Database(DATAHOST, DBUSER, DBPASSWORD, DATABASE);

$var_output = $db->get_pages();

include 'output.php';