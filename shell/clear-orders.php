<?php
/*
|------------------------------------------------------------------------------
| Clear orders
|------------------------------------------------------------------------------
|
| Usage: php -f clear-orders.php
|
*/

require '../app/Mage.php';
Mage::app('admin')->setUseSessionInUrl(false);                                                                                                                 

Mage::register('isSecureArea', true);

$sales = Mage::getModel("sales/order")->getCollection();
foreach ($sales as $order) {

  $orderId = $order->getIncrementId();

  try {
    $order->delete();
    echo "Order #" . $orderId . " is removed " . PHP_EOL;
  }
  catch (Exception $e) {
    echo "order #" . $orderId . " could not be remvoved: " .
      $e->getMessage() . PHP_EOL;
  }

} // foreach

echo "Complete." . PHP_EOL;
