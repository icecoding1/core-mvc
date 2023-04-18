<?php

namespace Patiphon\PhpCoreMvc;

date_default_timezone_set("Asia/bangkok");

use Patiphon\PhpCoreMvc\Application;
use \PDO;

class Database
{
  public static PDO $db; // fully qualify PDO class with "\"
  public static $dns;
  public static $username;
  public static $password;

  public function __construct(array $config)
  {
    self::$dns = $config['DB_DNS'];
    self::$username = $config['DB_USER'];
    self::$password = $config['DB_PASS'];

    self::$db = new PDO(self::$dns, self::$username, self::$password); // fully qualify PDO class with "\"
    self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // fully qualify PDO constants with "\"
    self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // fully qualify PDO constants with "\"

    return self::$db;
  }

  public function createMigrations()
  {
    $sql = "CREATE table IF NOT EXISTS migration (id int auto_increment primary key, name_menu varchar(255), created_date datetime)";
    return self::$db->exec($sql);
  }

  public function getTablerow()
  {
    $sql = "select name_menu from  migration";
    $select = self::$db->prepare($sql);
    $select->execute();
    $result = $select->fetchAll(PDO::FETCH_ASSOC);
    return  $result;
  }

  public function applyMigration()
  {
    $this->createMigrations();
    $row = $this->getTablerow();
    $arr = [];

    foreach ($row as $row) {
      array_push($arr, $row['name_menu']);
    }


    $assign = [];
    $files = scandir(Application::$ROOT_DIR . '/migrations');
    $arr_insert = array_diff($files, $arr);
    // print_r($arr_insert);

    foreach ($arr_insert as $migration) {
      if ($migration === '.' || $migration === '..') {
        continue;
      }

      // path migrations
      require_once Application::$ROOT_DIR . '/migrations/' . $migration;
      $className = pathinfo($migration, PATHINFO_FILENAME);
      $instance = new $className();
      $this->log("Applying migration $migration");
      $instance->up();
      $this->log("Applied migration $migration");
      $assign[]['menu'] = $migration;
    }

    if (!empty($assign)) {
      $this->Savemigration($assign);
    } else {
      $this->log("apply create migration not success because you apply all");
    }
  }


  public function downMigration()
  {
    $files = scandir(Application::$ROOT_DIR . '/migrations');
    foreach ($files as $migration) {
      if ($migration === '.' || $migration === '..') {
        continue;
      }

      // path migrations
      require_once Application::$ROOT_DIR . '/migrations/' . $migration;
      $className = pathinfo($migration, PATHINFO_FILENAME);
      $instance = new $className();
      $this->log("Down.. migration $migration");
      $instance->down();
      $this->log("Down success migration $migration");
    }

    $this->Delmigration();
    $this->log("Drop table migrations");
  }


  public function Savemigration(array $name_menu)
  {
    $date_now = date("Y-m-d H:i:s");
    foreach ($name_menu as $name_menu) {
      $sql = 'insert into migration (name_menu, created_date) values(?, ?)';
      $insert = self::$db->prepare($sql);
      $insert->execute([$name_menu['menu'], $date_now]);
    }
  }

  public function Delmigration()
  {
    $sql = 'drop table IF EXISTS migration';
    return self::$db->exec($sql);
  }

  public function log($message)
  {
    echo "(" . date("Y-m-d H:i:s") . ")   " . $message . PHP_EOL;
  }
}
