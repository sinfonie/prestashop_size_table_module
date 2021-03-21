<?php

/**
 * ScsDbModels
 * @author <sinfonie@o2.pl>
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

class ScsDbModels extends ObjectModel
{
  public $id_model;
  public $attr_group_id;
  public $dim_start;
  public $dim_end;
  public $name;
  public $properties;
  public $active;

  public static $definition = array(
    'table' => 'scs_models',
    'primary' => 'id_model',
    'multilang' => true,
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
        'lang' => true,
        'validate' => 'isString',
        'size' => 255
      ),
      'active' => array(
        'type' => self::TYPE_BOOL,
        'validate' => 'isBool',
        'required' => true,
      ),
    ),
  );

  public static function getModels(int $lang = null)
  {
    $query = new DbQuery();
    $query->select('m.`id_model`, m.`attr_group_id`, m.`dim_start`, m.`dim_end`, m.`name`, ml.`properties`, m.`active`, ml.id_lang');
    $query->from('scs_models', 'm');
    $query->leftJoin('scs_models_lang', 'ml', 'm.id_model = ml.id_model');
    if (!is_null($lang)) {
      $query->where('ml.`id_lang` = ' . pSQL($lang));
    }
    return self::dbRequest($query);
  }

  public static function getModel(int $id, int $lang = null)
  {
    $query = new DbQuery();
    $query->select('m.`id_model`, m.`attr_group_id`, m.`dim_start`, m.`dim_end`, m.`name`, ml.`properties`, m.`active`, ml.id_lang');
    $query->from('scs_models', 'm');
    $query->leftJoin('scs_models_lang', 'ml', 'm.id_model = ml.id_model');
    $query->where('m.`id_model` = ' . pSQL($id));
    if (!is_null($lang)) {
      $query->where('ml.`id_lang` = ' . pSQL($lang));
    }
    $result = self::dbRequest($query);
    if ($result && is_array($result)) {
      return $result[key($result)];
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
        $o = new self();
        $o->id_model = $v['id_model'];
        $o->attr_group_id = $v['attr_group_id'];
        $o->dim_start = $v['dim_start'];
        $o->dim_end = $v['dim_end'];
        $o->name = $v['name'];
        $o->active = $v['active'];
        if (isset($array[$v['id_model']])) {
          $o->properties = $array[$v['id_model']]->properties + [$v['id_lang'] => $v['properties']];
        } else {
          $o->properties = [$v['id_lang'] => $v['properties']];
        }
        $array[$v['id_model']] = $o;
      }
      return $array;
    }
  }
}
