<?php

namespace WarehouseShipping\Db;

class ProductApi {
  public static function addProduct($mysqli, $name, $dimensions, $weight){
    $stmt = $mysqli->prepare("INSERT INTO `product` (`name`, `dimensions`, `weight`) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $name, $dimensions, $weight);
    $stmt->execute();
    if ($stmt->affected_rows <= 0)
      throw new \Exception('Cannot add product, it probably already exists');
    $stmt->close();
  }
}
