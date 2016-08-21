<?php
/*
|------------------------------------------------------------------------------
| Clear customers
|------------------------------------------------------------------------------
|
| Usage: php -f clear-customers.php
|
*/

require '../app/Mage.php';
Mage::app('admin')->setUseSessionInUrl(false);                                                                                                                 
Mage::register('isSecureArea', true);

$customers = Mage::getModel("customer/customer")->getCollection();

foreach ($customers as $customer) {
  $id = $customer->getId();
  $customer->delete();
  echo "Customer deleted: " . $id . PHP_EOL;
}

echo "Complete." . PHP_EOL;
