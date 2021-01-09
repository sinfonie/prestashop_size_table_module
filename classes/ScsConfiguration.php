<?php

/**
 * ScsSql
 * @author Maciej Rumiński <ruminski.maciej@gmail.com>
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

class ScsConfiguration extends ObjectModel
{

  public $id;
  public $attr_group_id;
  public $dim_start;
  public $dim_end;
  public $properties;
  public $active;

  public static $definition = array(
    'table' => 'scs_configurations',
    'primary' => 'id',
    'multishop' => true,
    'fields' => array(
      'attr_group_id' => array(
        'type' => self::TYPE_INT,
        'validate' => 'isUnsignedInt',
      ),
      'dim_start' => array(
        'type' => self::TYPE_INT,
        'validate' => 'isUnsignedInt',
      ),
      'dim_end' => array(
        'type' => self::TYPE_INT,
        'validate' => 'isUnsignedInt',
        'allow_null' => true,
      ),
      'properties' => array(
        'type' => self::TYPE_STRING,
        'validate' => 'isString',
        'allow_null' => true,
        'size' => 128,
      ),
      'active' => array(
        'type' => self::TYPE_BOOL,
        'validate' => 'isBool',
        'allow_null' => true,

      ),
    ),
  );

  public static function getConfiguration($id)
  {
    $sql = 'SELECT `id`, `attr_group_id`, `dim_start`, `dim_end`, `properties`, `active` FROM `' . _DB_PREFIX_ . 'scs_configurations` ' .
      'WHERE `id` = ' . $id;
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
        $object->properties = $v['properties'];
        $object->active = $v['active'];
        $array[] = $object;
      }
      return $array;
    }
  }
}
