<?php

define('KEY', true); // ключ безопасности
include 'db.php';
$page_id = (int)$_POST['page_id'];

$myCurl = curl_init();
curl_setopt_array($myCurl, array(
    CURLOPT_URL => 'https://ru.wikipedia.org/w/api.php?origin=*&action=query&format=json&pageids=' . $page_id . '&prop=pageimages|extracts&piprop=original&explaintext=true',
    CURLOPT_RETURNTRANSFER => true
));
$response = curl_exec($myCurl);
$answer = json_decode($response, true);
curl_close($myCurl);
$myCurl = curl_init();
// Второй запрос нужен для того, что бы получить size и wordcount
curl_setopt_array($myCurl, array(
    CURLOPT_URL => 'https://ru.wikipedia.org/w/api.php?origin=*&action=query&format=json&list=search&srlimit=1&srsort=just_match&srsearch=' . urlencode($answer['query']['pages'][$page_id]['title']),
    CURLOPT_RETURNTRANSFER => true
));
$response = curl_exec($myCurl);
$answer_search = json_decode($response, true);
curl_close($myCurl);

if (isset($answer['query']['pages'][$page_id]['original']['source'])) // картинки может не оказаться, поэтому надо делать проверку
    $img_src = $answer['query']['pages'][$page_id]['original']['source'];
else
    $img_src = 'http://elinar-plast.ru/tpl/palitra/images/nophoto.jpg';

$sql = 'INSERT INTO `pages`
            VALUES(
                "",
                :page_id,
                :title,
                :plain_text,
                :img_src,
                :link,
                :size,
                :wordcount,
                :snippet
            )';
//Подготавливаем PDO выражение для SQL запроса
$stmt = $db->prepare($sql);
$stmt->bindValue(':page_id', $page_id, PDO::PARAM_STR);
$stmt->bindValue(':title', $answer['query']['pages'][$page_id]['title'], PDO::PARAM_STR);
$stmt->bindValue(':plain_text', $answer['query']['pages'][$page_id]['extract'], PDO::PARAM_STR);
$stmt->bindValue(':img_src', $img_src, PDO::PARAM_STR);
$stmt->bindValue(':link', 'https://ru.wikipedia.org/wiki/' . $answer['query']['pages'][$page_id]['title'], PDO::PARAM_STR);
$stmt->bindValue(':size', $answer_search['query']['search'][0]['size'], PDO::PARAM_STR);
$stmt->bindValue(':wordcount', $answer_search['query']['search'][0]['wordcount'], PDO::PARAM_STR);
$stmt->bindValue(':snippet', $answer_search['query']['search'][0]['snippet'], PDO::PARAM_STR);
$stmt->execute(); // запрос на создание записи


// Обработка plaine text. разбор его на слова-атомы

$plain_text = preg_replace("/\W/u", " ", $answer['query']['pages'][$page_id]['extract']); // убираем все знаки препинания
$lower_plain_text = mb_strtolower($plain_text); // уменьшаем все символы
$words = explode(" ", $lower_plain_text); // превращаем текст в массив

foreach($words as $word){
    if(!empty($word)){ // в массиве могут быть пустые строчки
        $sql = 'INSERT INTO `words`
            VALUES(
                "",
                :page_id,
                :word
            )';
        //Подготавливаем PDO выражение для SQL запроса
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':page_id', $page_id, PDO::PARAM_STR);
        $stmt->bindValue(':word', $word, PDO::PARAM_STR);
        $stmt->execute(); // запрос на создание записи
    }
    
}
