<?php
//Ключ защиты
if (!defined('KEY')) {
    exit('Нет ключа!');
}
include 'curlgetter.php';

//Логин БД
define('DBUSER', 'u227548_vlad');

//Пароль БД
define('DBPASSWORD', 'Ytljujkjdjkjvrf');

//БД
define('DATABASE', 'b227548_wikisearch');

//Хост
define('DATAHOST', '78.108.80.33');

class DataBase
{
    private $db;
    function __construct($dbhost, $dbuser, $dbpass, $dbname)
    {
        $this->wikiapi = new CurlGetter(); // 
        try { //Подключение к базе данных mySQL с помощью PDO
            $this->db = new PDO('mysql:host='.$dbhost.';dbname=' . $dbname, $dbuser, $dbpass, array(
                PDO::ATTR_PERSISTENT => true
            ));
            $sql = 'SET NAMES "UTF8"';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        } catch (PDOException $e) {
            print "Ошибка соединеия!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    public function query($sql, $params = [])
    {
        // Подготовка запроса
        $stmt = $this->db->prepare($sql);
        // Обход массива с параметрами и подставление значений
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
        }
        // Выполняем запрос
        $stmt->execute();
        // Возвращаем ответ
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get_pages()
    {
        return $this->query('SELECT * FROM `pages`');
    }
    public function save_page($page_id)
    {
        $answer = $this->wikiapi->query('https://ru.wikipedia.org/w/api.php?origin=*&action=query&format=json&pageids=' . $page_id . '&prop=pageimages|extracts&piprop=original&explaintext=true');
        // Второй запрос нужен для того, что бы получить size и wordcount
        $answer_search = $this->wikiapi->query('https://ru.wikipedia.org/w/api.php?origin=*&action=query&format=json&list=search&srlimit=1&srsort=just_match&srsearch=' . urlencode($answer['query']['pages'][$page_id]['title']));

        if (isset($answer['query']['pages'][$page_id]['original']['source'])) // картинки может не оказаться, поэтому надо делать проверку
            $img_src = $answer['query']['pages'][$page_id]['original']['source'];
        else
            $img_src = 'https://junior-it.ru/portfolio/volkodavov/wikisearch/images/nophoto.jpg'; // картинка отсутствия картинки

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
        $this->query($sql, [
            'page_id' => $page_id,
            'title' => $answer['query']['pages'][$page_id]['title'],
            'plain_text' => $answer['query']['pages'][$page_id]['extract'],
            'img_src' => $img_src,
            'link' => 'https://ru.wikipedia.org/wiki/' . $answer['query']['pages'][$page_id]['title'],
            'size' => $answer_search['query']['search'][0]['size'],
            'wordcount' => $answer_search['query']['search'][0]['wordcount'],
            'snippet' => $answer_search['query']['search'][0]['snippet']
        ]);
        $id_page_in_db = $this->db->lastInsertId(); // получаем id созданной записи


        // Обработка plaine text. разбор его на слова-атомы
        $plain_text = preg_replace("/\W/u", " ", $answer['query']['pages'][$page_id]['extract']); // убираем все знаки препинания
        $lower_plain_text = mb_strtolower($plain_text); // уменьшаем все символы
        $words = explode(" ", $lower_plain_text); // превращаем текст в массив

        foreach ($words as $word) {
            if (!empty($word)) { // в массиве могут быть пустые строчки
                $sql = 'INSERT INTO `words`
                        VALUES(
                            "",
                            :id_page,
                            :word
                        )';
                $this->query($sql, [
                    'id_page' => $id_page_in_db,
                    'word' => $word
                ]);
            }
        }

        $sql = 'SELECT *, COUNT(`word`) as "count"
                FROM `words`
                GROUP BY `word`, `id_page`
                HAVING `id_page`=:id_page'; // берем все слова, которые только что записали и группируем
        $rows = $this->query($sql, [
            'id_page' => $id_page_in_db
        ]);

        foreach ($rows as $row) {
            $sql = 'INSERT INTO `conn_pages_words`
                    VALUES(
                        "",
                        :id_word,
                        :id_page,
                        :count_words
                    )';
            $this->query($sql, [
                'id_word' => $row['id'],
                'id_page' => $id_page_in_db,
                'count_words' => $row['count']
            ]);
        }
    }
    public function search_pages($word){
        $sql = 'SELECT * FROM `pages` RIGHT JOIN (SELECT `id_page`, `count_words` FROM `conn_pages_words` RIGHT JOIN (SELECT `id` FROM `words` WHERE `word`=:word GROUP BY `word`, `id_page`) as `w1` ON `w1`.`id`=`conn_pages_words`.`id_word`) as `w2` ON `w2`.`id_page`=`pages`.`id` ORDER BY `count_words` DESC';
        return $this->query($sql, [
            'word' => $word
        ]);
    }
}
