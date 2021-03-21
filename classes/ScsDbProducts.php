<?php

/**
 * ScsDbProducts
 * @author <sinfonie@o2.pl>
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

class ScsDbProducts extends ObjectModel
{
  public $id;
  public $id_product;
  public $id_model;
  public $dimensions;
  public $status;

  const SCS_TABLE_NAME = 'scs_products';

  public static $definition = array(
    'table' => self::SCS_TABLE_NAME,
    'primary' => 'id',
    'fields' => array(
      'id_product' => array(
        'type' => self::TYPE_INT,
        'validate' => 'isUnsignedInt',
        'required' => true,
      ),
      'id_model' => array(
        'type' => self::TYPE_INT,
        'validate' => 'isUnsignedInt',
        'required' => true,
      ),
      'dimensions' => array(
        'type' => self::TYPE_STRING,
        'validate' => 'isString',
        'required' => true,
      ),
      'status' => array(
        'type' => self::TYPE_BOOL,
        'validate' => 'isBool',
        'required' => true,
      ),
    ),
  );

  public static function getProductDimensions(int $id)
  {
    $dbName = '`' . _DB_PREFIX_ . self::SCS_TABLE_NAME . '`';
    $sql = "SELECT `id`, `id_product`, `id_model`, `dimensions` FROM $dbName WHERE `id` = " . pSQL($id);
    $result = self::dbRequest($sql);
    if ($result && is_array($result)) {
      return $result[0];
    }
    return $result;
  }

  private static function dbRequest($sql)
  {
    $request = Db::getInstance()->executeS($sql);
    $array = [];
    if ($request === false) {
      return false;
    } else {
      foreach ($request as $v) {
        $object = new self();
        $object->id = $v['id'];
        $object->id_product = $v['id_product'];
        $object->id_model = $v['id_model'];
        $object->dimensions = $v['dimensions'];
        $array[] = $object;
      }
      return $array;
    }
  }
}
