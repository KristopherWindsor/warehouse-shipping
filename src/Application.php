<?php

namespace WarehouseShipping;

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
}
