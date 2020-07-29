<?php

/**
 * Class wrapper for working with PDO
 */

//DB::query() выполнить один запрос к БД с возвращением результата PDOStatement. его следует использовать только в том случае, если запрос не содержит необработанные данные, например из внешних источников.
//DB::getRow() вернёт из БД одну запись (одна, категория, один пост и тп).
//DB::getRows() вернёт из БД все записи (или несколько по условию).
//DB::getValue() вернёт из БД одно значение (название категории).
//DB::getColumn() вернёт из БД значения одной колонки (все названия категорий).
//DB::sql() произвольный запрос.

require_once('configPDO.php');

class DB
{
    /**
     * Настройки подключения
     * Лучше выносить в конфиг
     * self::DB_HOST -> Config::DB_HOST
     */

//    const DB_HOST = '127.0.0.1'; // localhost
//    const DB_USER = 'root';
//    const DB_PASSWORD = 'gfhjkm84286252';
//    const DB_NAME = 'test_oop';
//    const CHARSET = 'utf8';
//    const DB_PREFIX = '';

    /**
     * @var PDO
     */
    static private $db;

    /**
     * @var null
     */
    protected static $instance = null;

    /**
     * DB constructor.
     * @throws Exception
     */
    public function __construct(){
        if (self::$instance === null){
            try {
                self::$db = new PDO(
                    'mysql:host='.Config::DB_HOST.';dbname='.Config::DB_NAME,
                    Config::DB_USER,
                    Config::DB_PASSWORD,
                    $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".Config::CHARSET
                    ]
                );
            } catch (PDOException $e) {
                throw new Exception ($e->getMessage());
            }
        }
        return self::$instance;
    }

    /**
     * @param $stmt
     * @return PDOStatement
     */
    public static function query($stmt)  {
        return self::$db->query($stmt);
    }

    /**
     * @param $stmt
     * @return PDOStatement
     */
    public static function prepare($stmt)  {
        return self::$db->prepare($stmt);
    }

    /**
     * @param $query
     * @return int
     */
    static public function exec($query) {
        return self::$db->exec($query);
    }

    /**
     * @return string
     */
    static public function lastInsertId() {
        return self::$db->lastInsertId();
    }

    /**
     * @param $query
     * @param array $args
     * @return PDOStatement
     * @throws Exception
     */
    public static function run($query, $args = [])  {
        try{
            if (!$args) {
                return self::query($query);
            }
            $stmt = self::prepare($query);
            $stmt->execute($args);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $query
     * @param array $args
     * @return mixed
     */
    public static function getRow($query, $args = [])  {
        return self::run($query, $args)->fetch();
    }

    /**
     * @param $query
     * @param array $args
     * @return array
     */
    public static function getRows($query, $args = [])  {
        return self::run($query, $args)->fetchAll();
    }

    /**
     * @param $query
     * @param array $args
     * @return mixed
     */
    public static function getValue($query, $args = [])  {
        $result = self::getRow($query, $args);
        if (!empty($result)) {
            $result = array_shift($result);
        }
        return $result;
    }

    /**
     * @param $query
     * @param array $args
     * @return array
     */
    public static function getColumn($query, $args = [])  {
        return self::run($query, $args)->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function sql($query, $args = [])
    {
        self::run($query, $args);
    }
}


//ПРИМЕРЫ
//Примеры использованияВыбрать одну категорию по id:
//$data = $db::getRow("SELECT * FROM `category` WHERE `id` = ?", [$id]);

//Выбрать имена всех категорий у которых parent_id больше $parent_id:
//$data = $db::getRows("SELECT `title` FROM `category` WHERE `parent_id` > ?", [$parent_id]);
//foreach ($data as $item) {
//echo $item['title'].'<br>';
//}

//Выбрать имя категории по id:
//$data = $db::getValue("SELECT `title` FROM `category` WHERE `id` > ?", [$id]);

//Выбрать имена всех категорий:
//$data = $db::getColumn("SELECT `title` FROM `category`");
//foreach ($data as $item) {
//echo $item.'<br>';
//}

//Вставить запись в БД:
//$query = "INSERT INTO `category` (
//`title`,
//`alias`,
//`parent_id`,
//`keywords`,
//`description`
//)
//VALUES (
//:title,
//:alias,
//:parent_id,
//:keywords,
//:description
//)";
//$args = [
//'title' => $title,
//'alias' => $alias,
//'parent_id' => intval($parent_id),
//'keywords' => $keywords,
//'description' => $description
//];
//$db::sql($query, $args)

//Обновить запись в БД:
//$query = "UPDATE `category`
//SET `title` = :title
//WHERE `id` = :id";
//$args = [
//'id' => $id,
//'title' => $title
//];
//$db::sql($query, $args)

//Удалить запись:
//$db::sql("DELETE FROM `category` WHERE `id` = ?", [$id]);

