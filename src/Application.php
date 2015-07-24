<?php

namespace WarehouseShipping;

/* This class runs the command-line application
 */
class Application {

  private $mysql;

  public function __construct($argc, $argv){
    try {
      $this->mysqli = Db\Connection::createMysqli();
    } catch (\Exception $e){
      die( "Please config this application for your system. " . $e->getMessage() . "\n" );
    }

    try {
      if ($argc == 5 && $argv[1] == 'add' && $argv[2] == 'warehouse')
        $this->addWarehouse($argv[3], $argv[4]);
      else if ($argc == 6 && $argv[1] == 'add' && $argv[2] == 'product')
        $this->addProduct($argv[3], $argv[4], $argv[5]);
      else if ($argc == 6 && $argv[1] == 'add' && $argv[2] == 'inventory' && $argv[5] > 0)
        $this->addInventory($argv[3], $argv[4], $argv[5]);
      else if ($argc == 3 && $argv[1] == 'order')
        $this->order($argv[2]);
      else
        $this->help();
    } catch (\Exception $e){
      die("Error: " . $e->getMessage() . "\n");
    }
  }

  private function help(){
    ?>Usage:
php warehouse-shipping.php add warehouse <name> <address>
php warehouse-shipping.php add product <name> <dimensions> <weight>
php warehouse-shipping.php add inventory <warehouse> <product> <quantity>
php warehouse-shipping.php order <destination address>
<?php
  }

  private function addWarehouse($name, $address){
    $geo = GeoLookup::getLatLon($address);
    Db\WarehouseApi::addWarehouse($this->mysqli, $name, $geo[2], $geo[0], $geo[1]);
    echo "OK\n";
  }

  private function addProduct($name, $dimensions, $weight){
    Db\ProductApi::addProduct($this->mysqli, $name, $dimensions, $weight);
    echo "OK\n";
  }

  private function addInventory($warehouse, $product, $quantity){
    $warehouse = Db\WarehouseApi::getWarehouse($this->mysqli, $warehouse);
    $product = Db\ProductApi::getProduct($this->mysqli, $product);
    Db\WarehouseApi::addProducts($this->mysqli, $warehouse->id, $product->id, $quantity);
    echo "OK\n";
  }

  private function order($dest_address){
    $geo = GeoLookup::getLatLon($dest_address);
    $orders_by_name = $orders_by_id = [];
    echo "You want to create an order shipping to " . $geo[2];?>

Enter one product name per line, empty line when order is done
Optionally, enter <product name>=<quantity> to add multiple

><?php
    // allow user to enter the items for this order
    $stdin = fopen('php://stdin', 'r');
    while ($line = trim(fgets($stdin))){
      // handle optional <product name>=<quantity>
      $tmp = strrpos($line, '=');
      if ($tmp !== false && ctype_digit(substr($line, $tmp + 1))){
        $quantity = substr($line, $tmp + 1);
        $line = substr($line, 0, $tmp);
      } else {
        $quantity = 1;
      }

      // attempt to find product, add it to the order
      try {
        $product = Db\ProductApi::getProduct($this->mysqli, $line);
        echo "Product added to order (quantity=" . $quantity . ")\n>";
      } catch (\Exception $e){
        echo "Cannot find the product -- please try again\n>";
        continue;
      }
      @$orders_by_name[$product->name] += $quantity;
      @$orders_by_id[$product->id] += $quantity;
    }
    fclose($stdin);

    echo "Order Summary:\n";
    foreach ($orders_by_name as $product => $quantity){
      echo '  ' . substr( $product . str_repeat('.', 30), 0, 30 ) . ' ' . $quantity . "\n";
    }

    // get all warehouses with the required inventory, then pick the closest one
    $warehouses = Db\WarehouseApi::getStockedWarehouses($this->mysqli, $orders_by_id);
    $closest = null;
    $best_dist = null;
    foreach ($warehouses as $i){
      $this_dist = GeoMath::vincentyGreatCircleDistance($geo[0], $geo[1], $i->lat, $i->lon);
      if ($best_dist === null || $this_dist < $best_dist){
        $closest = $i;
        $best_dist = $this_dist;
      }
    }

    // final results
    if ($closest === null){
      echo "\nNo single warehouse has all of the items requested. Sorry\n";
    } else {
      echo "\nThis order will be fulfilled by this warehouse: " . $closest->name . "\n";
      echo "The order destination is " . number_format($best_dist, 1) . "km away from the warehouse.\n";
    }
  }
}
