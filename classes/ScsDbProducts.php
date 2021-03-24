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
  public $id_property;
  public $dim_start;
  public $dim_end;
  public $status;

  public static $definition = array(
    'table' => 'scs_products',
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
      'id_property' => array(
        'type' => self::TYPE_INT,
        'validate' => 'isUnsignedInt',
        'required' => true,
      ),
      'dim_start' => array(
        'type' => self::TYPE_INT,
        'validate' => 'isUnsignedInt',
        'required' => true,
      ),
      'dim_end' => array(
        'type' => self::TYPE_INT,
        'validate' => 'isUnsignedInt',
        'required' => true,
      ),
      'active' => array(
        'type' => self::TYPE_BOOL,
        'validate' => 'isBool',
        'required' => true,
      ),
    ),
  );

  public static function getProductModelsDimensions(int $id_product, int $id_model = null, bool $active = null)
  {
    $query = new DbQuery();
    $query->select('`id`, `id_product`, `id_model`, `dim_start`, `dim_end`, `active`');
    $query->from('scs_products');
    $query->where('id_product = ' . pSQL($id_product));
    if (!is_null($id_model)) {
      $query->where('id_model = ' . pSQL($id_model));
    }
    if (!is_null($active)) {
      $query->where('active = ' . pSQL($active));
    }
    return self::dbRequest($query);
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
        $object->id_property = $v['id_property'];
        $object->dim_start = $v['dim_start'];
        $object->dim_end = $v['dim_end'];
        $object->active = $v['active'];
        $array[] = $object;
      }
      return $array;
    }
  }
}
