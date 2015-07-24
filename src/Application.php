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

    if ($argc == 5 && $argv[1] == 'add' && $argv[2] == 'warehouse')
      $this->createWarehouse();
    else if ($argc == 6 && $argv[1] == 'add' && $argv[2] == 'product')
      $this->createProduct();
    else if ($argc == 6 && $argv[1] == 'add' && $argv[2] == 'inventory')
      $this->addInventory();
    else if ($argc == 2 && $argv[1] == 'order')
      $this->order();
    else
      $this->help();
  }

  private function help(){
    ?>Usage:
php warehouse-shipping.php add warehouse <name> <address>
php warehouse-shipping.php add product <name> <dimensions> <weight>
php warehouse-shipping.php add inventory <warehouse> <product> <quantity>
php warehouse-shipping.php order
<?php
  }
}
