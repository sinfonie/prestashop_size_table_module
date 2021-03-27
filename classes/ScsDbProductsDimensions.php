<?php

/**
 * ScsDbProductsDimensions
 * @author <sinfonie@o2.pl>
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

class ScsDbProductsDimensions extends ObjectModel
{
  public $id_dimension;
  public $id_product_model;
  public $id_property;
  public $dim_start;
  public $dim_end;
  public $active;

  public static $definition = array(
    'table' => 'scs_products_dimensions',
    'primary' => 'id_dimension',
    'fields' => array(
      'id_product_model' => array(
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

  public static function getProductsDimensions(int $id_product_model, int $id_property = null, bool $active = null)
  {
    $query = new DbQuery();
    $query->select('`id_dimension`, `id_product_model`, `id_property`, `dim_start`, `dim_end`, `active`');
    $query->from('scs_products_dimensions');
    $query->where('id_product_model = ' . pSQL($id_product_model));
    if (!is_null($id_property)) {
      $query->where('id_property = ' . pSQL($id_property));
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
        $object->id_dimension = $v['id_dimension'];
        $object->id_product_model = $v['id_product_model'];
        $object->id_property = $v['id_property'];
        $object->dim_start = $v['dim_start'];
        $object->dim_end = $v['dim_end'];
        $object->active = $v['active'];
        $array[] = $object;
      }
      return $array;
    }
  }

  public static function saveProductDimensions(int $id_product_model, int $id_property, int $dim_start_value, int $dim_end_value): void
  {
    $pd = new ScsDbProductsDimensions;
    $pd->id_product_model = $id_product_model;
    $pd->id_property = $id_property;
    $pd->dim_start = $dim_start_value;
    $pd->dim_end = $dim_end_value;
    $pd->active = true;
    $pd->save();
  }

  public static function updateDimension(ScsDbProductsDimensions $pd, int $dim_start_value, int $dim_end_value): void
  {
    $pd->dim_start = $dim_start_value;
    $pd->dim_end = $dim_end_value;
    $pd->id = $pd->id_dimension;
    $pd->update();
  }
}
