<?php
/*
|------------------------------------------------------------------------------
| Clear unused images
|------------------------------------------------------------------------------
|
| Usage: php -f clear-images.php
|
| Credits:
|
| - https://gist.github.com/aleron75/07ab2a950b2e3429a820
|
*/

require_once 'abstract.php';

class Mage_Shell_ClearImages extends Mage_Shell_Abstract
{

  const CATALOG_PRODUCT = '/catalog/product';

  const CACHE = '/cache/';
  const PLACEHOLDER = '/placeholder/';


  public function run()
  {
    if (!$this->getArg('run')) {
      echo $this->usageHelp();
      return;
    }

    $remove = $this->getArg('remove') ? true : false ;
    $dryrun = !$remove;
    $debug = $this->getArg('debug');
    $includeCache = $this->getArg('cache');
    $media = Mage::getBaseDir('media');

    $imagesOnDb = array();
    $imagesOnDisk = array();
    $setup = new Mage_Core_Model_Resource_Setup('core_setup');
    /** @var Varien_Db_Adapter_Pdo_Mysql $connection */
    $connection = $setup->getConnection();

    $sql = "
      SELECT DISTINCT `value`
      FROM (
        SELECT `value`
        FROM  `{$setup->getTable('catalog_product_entity_media_gallery')}`
        WHERE `attribute_id`
        IN (
          SELECT `attribute_id` 
          FROM `{$setup->getTable('eav_attribute')}` 
          WHERE 
            `attribute_code` IN ('media_gallery') AND 
            `entity_type_id` = 4
        )
        UNION
        SELECT `value`
        FROM  `{$setup->getTable('catalog_product_entity_varchar')}`
        WHERE `attribute_id`
        IN (
          SELECT `attribute_id` 
          FROM `{$setup->getTable('eav_attribute')}` 
          WHERE 
            `attribute_code` IN ('image','small_image','thumbnail') AND 
            `entity_type_id` = 4
        )
      ) AS T
    ";

    $result = $connection->query($sql);
    foreach ($result->fetchAll() as $rec) {
      $imagesOnDb[$rec['value']] = 1;
    }
    $imagesOnDb = array_keys($imagesOnDb);
    if ($debug) {
      print_r(array_slice($imagesOnDb, 0, 100));
    }

    if ($debug) {
      echo $media . "/*\n";
    }
    $skip = strlen($media . self::CATALOG_PRODUCT);
    $images = $this->_glob_recursive(
      $media . self::CATALOG_PRODUCT . '/*', 
      GLOB_MARK
    );
    foreach ($images as $img) {
      if (substr($img, -1) != '/') {
        if ((substr($img, $skip, 13) != self::PLACEHOLDER)  
            && ($includeCache || (substr($img, $skip, 7) != self::CACHE))) {
          $imagesOnDisk[] = substr($img, $skip);
        }
      }
    } // foreach

    if ($debug) {
      print_r(array_slice($imagesOnDisk, 0, 100));
    }

    $imagesToDelete = array_diff($imagesOnDisk, $imagesOnDb);
    if ($debug) {
      print_r($imagesToDelete);
      echo count($imagesOnDisk)." images on Disk\n";
      echo count($imagesOnDb)." images on DB\n";
      echo count($imagesToDelete)." images to delete\n";
    } 
    else {
      foreach ($imagesToDelete as $x) {
        if ($dryrun) {
          echo 'rm ' . $media . self::CATALOG_PRODUCT . $x.PHP_EOL;
        } 
        else {
          @unlink($media . self::CATALOG_PRODUCT . $x);
        }
      } // foreach $imagesToDelete
    }

    if ($debug) {
      echo "\r\n";
    }

    return;
  }


  public function usageHelp()
  {
    return <<<USAGE
Usage: php -f clear-images.php
    run         run the script (as default will run in dry mode)
    remove      remove images
    cache       remove cache images too
    debug       debug mode


USAGE;
  }


  private function _glob_recursive($pattern, $flags = 0)
  {
    $files = glob($pattern, $flags);
    $dirs = glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT);
    foreach ($dirs as $dir) {
      $files = array_merge(
        $files, 
        $this->_glob_recursive($dir . '/' . basename($pattern), $flags)
      );
    }
    return $files;
  }


} // class

$shell = new Mage_Shell_ClearImages();
$shell->run();
