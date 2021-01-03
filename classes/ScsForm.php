<?php

if (!defined('_PS_VERSION_')) {
  exit;
}

class ScsForm
{

  protected static $module = false;

  public static function init($module)
  {
    if (self::$module == false) {
      self::$module = $module;
    }
    return self::$module;
  }

  /**
   * Names of values assigned to each attribute
   */
  private static $confNames = [
    'active',
    'first_size',
    'second_size',
  ];

  /**
   * Method returns an set of values ready to use in prestashop forms
   */
  public static function createConfValues(array $group): array
  {
    return array_map(function ($name, $group) {
      $field = self::$module->modPrefix . 'group_' . $group['id_attribute_group'] . '_' . $name;
      return [
        'field' => $field,
        'field_upper' => strtoupper($field),
        'field_name' => self::$module->l($name),
        'group_id' => $group['id_attribute_group'],
        'group_name' => $group['name'],
      ];
    }, self::$confNames, array_fill_keys(self::$confNames, $group));
  }

  /**
   * Update method
   */
  public function onSubmit($group): string
  {
    $confValues = self::createConfValues($group);
    $output = '';
    foreach ($confValues as $confVal) {
      $getValue = Tools::getValue($confVal['field']);
      if ($getValue !== false) {
        $value = strval($getValue);
        $text = ': ' . $confVal['field_name'] . ' (' . $group['name'] . ')';
        if (!Validate::isGenericName($value)) {
          $output .= self::$module->displayError(self::$module->l('Invalid Configuration value') . $text);
        } else {
          if ($value !== Configuration::get($confVal['field_upper'])) {
            Configuration::updateValue($confVal['field_upper'], $value);
            $output .= self::$module->displayConfirmation(self::$module->l('Settings updated') . $text);
          }
        }
      }
    }
    return $output;
  }
}
