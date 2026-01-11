<?php
    require_once __DIR__ . '/../auth.php';
class Database {
  private static ?PDO $pdo = null;

  public static function pdo(): PDO {
    if (self::$pdo === null) {
      $cfg = require __DIR__ . '/../config/config.php';
      self::$pdo = new PDO(
        $cfg['db']['dsn'],
        $cfg['db']['user'],
        $cfg['db']['pass'],
        $cfg['db']['options']
      );
    }
    return self::$pdo;
  }
}
