<?php

namespace WarehouseShipping\Db;

class WarehouseApi {
  public static function addWarehouse($mysqli, $name, $address, $lat, $lon){
    $stmt = $mysqli->prepare("INSERT INTO `warehouse` (`name`, `address`, `lat`, `lon`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssdd', $name, $address, $lat, $lon);
    $stmt->execute();
    $problem = ($stmt->affected_rows <= 0);
    $stmt->close();
    if ($problem)
      throw new \Exception('Cannot add warehouse, it probably already exists');
  }

  public static function getWarehouse($mysqli, $name){
    $stmt = $mysqli->prepare("SELECT * FROM `warehouse` WHERE `name` = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_object();
    $stmt->close();
    if ($row)
      return $row;
    throw new \Exception('Warehouse not found');
  }

  public static function addProducts($mysqli, $warehouse_id, $product_id, $quantity){
    $stmt = $mysqli->prepare("
      INSERT INTO `warehouse_products` VALUES (?, ?, ?)
      ON DUPLICATE KEY UPDATE `quantity` = `quantity` + ?; ");
    $stmt->bind_param('iiii', $warehouse_id, $product_id, $quantity, $quantity);
    $stmt->execute();
    $stmt->close();
  }
}
