<?php

namespace WarehouseShipping\Db;

class WarehouseApi {
  public static function addWarehouse($mysqli, $name, $address, $lat, $lon){
    $stmt = $mysqli->prepare("INSERT INTO `warehouse` (`name`, `address`, `lat`, `lon`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssdd', $name, $address, $lat, $lon);
    $stmt->execute();
    if ($stmt->affected_rows <= 0)
      throw new \Exception('Cannot add warehouse, it probably already exists');
    $stmt->close();
  }
}
