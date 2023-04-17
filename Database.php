<?php

namespace app\core;

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
}
