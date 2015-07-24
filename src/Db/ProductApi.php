<?php

namespace WarehouseShipping\Db;

/* Class with queries for product-related reads/writes
 */
class ProductApi {
  public static function addProduct($mysqli, $name, $dimensions, $weight){
    $stmt = $mysqli->prepare("INSERT INTO `product` (`name`, `dimensions`, `weight`) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $name, $dimensions, $weight);
    $stmt->execute();
    if ($stmt->affected_rows <= 0)
      throw new \Exception('Cannot add product, it probably already exists');
    $stmt->close();
  }

  public static function getProduct($mysqli, $name){
    $stmt = $mysqli->prepare("SELECT * FROM `product` WHERE `name` = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_object())
      return $row;
    throw new \Exception('Product not found');
  }
}
