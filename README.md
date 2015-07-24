# warehouse-shipping
CLI tool for managing warehouse inventory, products, and orders

## intro
This is a homework problem from S.h.i.p.w.i.r.e.
I met all of the feature requirements. Rather than having an app build up the warehouses, etc, all in memory, I wrote a script that stores this data in MySQL. And rather than having the tool driven through a main menu, it is driven through command-line parameters.

## requirements
* PHP 5.5
* mysqli
* composer

## install
````
git clone <url>
cd warehouse-shipping
composer install
mysql ....... < scripts/init_db.sql
vi inc/database.php
````

## usage
````
Usage:
php warehouse-shipping.php add warehouse <name> <address>
php warehouse-shipping.php add product <name> <dimensions> <weight>
php warehouse-shipping.php add inventory <warehouse> <product> <quantity>
php warehouse-shipping.php order <destination address>
````

## example
````
[$] php warehouse-shipping.php order "mlk library, san jose"
You want to create an order shipping to Dr. Martin Luther King, Jr. Library, San José State University, 150 East San Fernando Street, San Jose, CA 95112, USA
Enter one product name per line, empty line when order is done
Optionally, enter <product name>=<quantity> to add multiple

>ipod
Product added to order (quantity=1)
>
Order Summary:
  ipod.......................... 1

This order will be fulfilled by this warehouse: sjsu
The order destination is 0.3km away from the warehouse.
````
````
[$] php warehouse-shipping.php order "mlk library, san jose"
You want to create an order shipping to Dr. Martin Luther King, Jr. Library, San José State University, 150 East San Fernando Street, San Jose, CA 95112, USA
Enter one product name per line, empty line when order is done
Optionally, enter <product name>=<quantity> to add multiple

>ipod
Product added to order (quantity=1)
>boba
Product added to order (quantity=1)
>
Order Summary:
  ipod.......................... 1
  boba.......................... 1

No single warehouse has all of the items requested. Sorry
````
````
[$] php warehouse-shipping.php add inventory "axcient" "ipod" 10
OK
````
````
[$] php warehouse-shipping.php order "mlk library, san jose"
You want to create an order shipping to Dr. Martin Luther King, Jr. Library, San José State University, 150 East San Fernando Street, San Jose, CA 95112, USA
Enter one product name per line, empty line when order is done
Optionally, enter <product name>=<quantity> to add multiple

>ipod
Product added to order (quantity=1)
>boba
Product added to order (quantity=1)
>
Order Summary:
  ipod.......................... 1
  boba.......................... 1

This order will be fulfilled by this warehouse: axcient
The order destination is 21.2km away from the warehouse.
````