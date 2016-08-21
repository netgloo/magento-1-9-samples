<?php
/*
|------------------------------------------------------------------------------
| Get the product image
|------------------------------------------------------------------------------
|
| Get the product image (jpg/png) by its sku:
|
|     http://example.com/extra/product-image.php?s=1231
|
*/

require_once "../app/Mage.php";

Mage::app('admin');

if (!isset($_GET['s'])) {
  header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad request", true, 400);
  exit();
}

// Get the sku
$sku = $_GET['s'];

// Get the product name
// $name = $_GET['n'];

// Get the product by sku
$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

// If the product does not exists return 404
if ($product === false) {
  header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
  exit();
}

// Get the product media config object
$productMediaConfig = Mage::getModel('catalog/product_media_config');
$imageUrl = $productMediaConfig->getMediaUrl($product->getImage());

// Get the image name
$imageName = basename($product->getImage());

// Compute the content type
$contentType = "Content-type: image/jpeg";
if (substr($imageName, -4) === ".png") {
  $contentType = "Content-type: image/png";
}

// Set http headers
header($contentType);
header("Content-Disposition: inline; filename=\"{$imageName}\"");

// Returns the image
echo file_get_contents($imageUrl);
