<?php

/**
 * ScsSql
 * @author Maciej Rumiński <ruminski.maciej@gmail.com>
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

class ScsDbModels extends ObjectModel
{

  public $id;
  public $attr_group_id;
  public $dim_start;
  public $dim_end;
  public $name;
  public $properties;
  public $active;

  public static $definition = array(
    'table' => 'scs_models',
    'primary' => 'id',
    'multilang' => true,
    'multilang_shop' => true,
    'fields' => array(
      'attr_group_id' => array(
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
      'name' => array(
        'type' => self::TYPE_STRING,
        'validate' => 'isString',
        'size' => 64,
      ),
      'properties' => array(
        'type' => self::TYPE_STRING,
        'validate' => 'isString',
        'required' => true,
      ),
      'active' => array(
        'type' => self::TYPE_BOOL,
        'validate' => 'isBool',
        'required' => true,
      ),
    ),
  );

  public static function getModels()
  {
    $sql = 'SELECT `id`, `attr_group_id`, `dim_start`, `dim_end`, `name`, `properties`, `active` FROM `' . _DB_PREFIX_ . 'scs_models`';
    return self::dbRequest($sql);
  }

  public static function getModel(int $id)
  {
    $sql = 'SELECT `id`, `attr_group_id`, `dim_start`, `dim_end`, `name`, `properties`, `active` FROM `' . _DB_PREFIX_ . 'scs_models` ' .
      'WHERE `id` = ' . pSQL($id);
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
        $object->attr_group_id = $v['attr_group_id'];
        $object->dim_start = $v['dim_start'];
        $object->dim_end = $v['dim_end'];
        $object->name = $v['name'];
        $object->properties = $v['properties'];
        $object->active = $v['active'];
        $array[] = $object;
      }
      return $array;
    }
  }
}
