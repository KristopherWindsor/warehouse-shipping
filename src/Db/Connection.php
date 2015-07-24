<?php

namespace WarehouseShipping\Db;

class Connection {
  public static function createMysqli(){
    $db_file = dirname( dirname( __DIR__ ) ) . '/inc/database.php';
    if (!file_exists($db_file))
      throw new \Exception('No database configuration found');
    require_once $db_file;

    @$mysqli = new \mysqli("$host:$port", $user, $pass, $db);
    if ($mysqli->connect_errno)
      throw new \Exception('Database connection failed');
    return $mysqli;
  }
}
