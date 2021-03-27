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
  public $id_product_model;
  public $id_product;
  public $id_model;
  public $active;

  public static $definition = array(
    'table' => 'scs_products',
    'primary' => 'id_product_model',
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
      'active' => array(
        'type' => self::TYPE_BOOL,
        'validate' => 'isBool',
        'required' => true,
      ),
    ),
  );

  public static function getProducts(int $id_product = null, int $id_model = null, bool $active = null)
  {
    $query = new DbQuery();
    $query->select('`id_product_model`, `id_product`, `id_model`, `active`');
    $query->from('scs_products');
    if (!is_null($id_product)) {
      $query->where('id_product = ' . pSQL($id_product));
    }
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
        $o = new self();
        $o->id_product_model = $v['id_product_model'];
        $o->id_product = $v['id_product'];
        $o->id_model = $v['id_model'];
        $o->active = $v['active'];
        $array[] = $o;
      }
      return $array;
    }
  }

  public static function saveProduct(int $id_product, int $id_model): int
  {
    $p = new ScsDbProducts;
    $p->id_product = $id_product;
    $p->id_model = $id_model;
    $p->active = true;
    $p->save();
    return $p->id;
  }
}
