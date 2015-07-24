<?php

namespace WarehouseShipping\Db;

/* Class with queries for warehouse-related reads/writes
 */
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

  /* Add to the inventory of a given product at a given warehouse
   */
  public static function addProducts($mysqli, $warehouse_id, $product_id, $quantity){
    $stmt = $mysqli->prepare("
      INSERT INTO `warehouse_products` VALUES (?, ?, ?)
      ON DUPLICATE KEY UPDATE `quantity` = `quantity` + ?; ");
    $stmt->bind_param('iiii', $warehouse_id, $product_id, $quantity, $quantity);
    $stmt->execute();
    $stmt->close();
  }

  /* Return all warehouses that have sufficient inventory to fulfill an order (in no specified order).
   * @param array $quantities_requested a <product id> -> <quantity> map
   * @return generator
   */
  public static function getStockedWarehouses($mysqli, $quantities_requested){
    $subqueries = [];
    foreach ($quantities_requested as $product_id => $quantity)
      $subqueries[] = sprintf('`id` IN(
          SELECT `id` FROM `warehouse`
          LEFT JOIN `warehouse_products` ON `warehouse`.`id` = `warehouse_products`.`warehouse_id`
          WHERE `product_id` = %d AND `quantity` >= %d
        )', $product_id, $quantity);
    $sql = 'SELECT * FROM `warehouse` ';
    if ($subqueries)
      $sql .= 'WHERE ' . implode(' AND ', $subqueries);

    $result = $mysqli->query($sql);
    while ($obj = $result->fetch_object())
      yield $obj;
    $result->close();
  }
}
