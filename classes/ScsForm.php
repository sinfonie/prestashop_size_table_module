<?php

/**
 * ScsForm class is responsible for managing and creating a configuration form for the module
 * @author Maciej Rumiński <ruminski.maciej@gmail.com>
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

class ScsForm
{
  protected static $module = false;

  public static function init(SinClothesSizing $module)
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
    'is_active',
    'start',
    'end',
  ];

  /**
   * Method sets translations for $confNames
   */
  private static function translateLabels($name)
  {
    switch ($name) {
      case 'is_active':
        return self::$module->l('is active');
        break;
      case 'start':
        return self::$module->l('start');
        break;
      case 'end':
        return self::$module->l('end');
        break;
    }
  }

  /**
   * Method returns all attribute values ready to use in prestashop forms
   * @param array $attributesGroups given attribute array
   * @return array array
   */
  public static function getConfValues(array $attributesGroups): array
  {
    foreach ($attributesGroups as $group) {
      $groupConfAttributes = self::createConfValues($group);
      foreach ($groupConfAttributes as $confAttribute) {
        $output[$group['id_attribute_group']][$confAttribute['field_name']] = $confAttribute;
      }
    }
    return $output;
  }

  /**
   * Method returns an set of values ready to use in prestashop forms
   * @param array $group given attribute array
   * @return array array in "confValueFormat" used in several method in this class
   */
  public static function createConfValues(array $group): array
  {
    return array_map(function ($name, $group) {
      $field = self::$module->modPrefix . 'group_' . $group['id_attribute_group'] . '_' . $name;
      return [
        'field_lower' => $field,
        'field_upper' => strtoupper($field),
        'label' => self::translateLabels($name),
        'group_id' => $group['id_attribute_group'],
        'group_name' => $group['name'],
        'field_name' => $name,
      ];
    }, self::$confNames, array_fill_keys(self::$confNames, $group));
  }

  /**
   * Method checks for submit and call onSubmit method for each given attribute group
   * @param array $confValues array of arrays in "confValueFormat"
   * @return array array with all given groups alert strings
   */
  public static function submitForm(array $confValues): array
  {
    $output = [];
    if (Tools::isSubmit(self::$module->name . '_submit')) {
      $output = array_map('self::onSubmit', $confValues);
    }
    return $output;
  }

  /**
   * Method return array with with 'fields' and 'values' keys
   * @param array $attributesGroups
   * @param array $confValues
   * @return array
   */
  public static function getFormData($attributesGroups, $confValues): array
  {
    $activeAttributes =  self::getActiveAttributes($attributesGroups, $confValues);
    $form = [];
    $elements = [];
    foreach ($confValues as $groupId => $groupConfValues) {
      $form['values'][$groupConfValues['is_active']['field_lower']] = Configuration::get($groupConfValues['is_active']['field_upper']);
      $elements['group_radio'][] = self::elementGroupRadio($groupConfValues['is_active']);
      if (in_array($groupId, $activeAttributes)) {
        $sliced = self::getSlicedAtrributesArray($groupId);
        foreach ($groupConfValues as $confValue) {
          if ($confValue['field_name'] !== 'is_active') {
            $form['values'][$confValue['field_lower']] = Configuration::get($confValue['field_upper']);
            $elements['dimension_select'][] = self::elementDimensionSelect($confValue, $sliced);
          }
        }
      }
    }
    $form['values'] = array_merge($form['values'], self::getAttributesDescription());

    if (!empty($form)) {
      $form['fields'][] = self::groupsForm($elements['group_radio']);
      $form['fields'][] = self::dimensionsForm($elements['dimension_select']);
    } else {
      $form['fields'] = [];
    }
    return $form;
  }

  /**
   * Method update values and return alert strings
   * @param array $confValues array of arrays in "confValueFormat"
   * @return array string with specific group alert strings
   */
  private static function onSubmit(array $confValues): string
  {
    $output = '';
    foreach ($confValues as $confVal) {
      $getValue = Tools::getValue($confVal['field_lower']);
      if ($getValue !== false) {
        $value = strval($getValue);
        $text = ' -  ' . ucfirst($confVal['label']) . ' (' . $confVal['group_name'] . ')';
        if (!Validate::isGenericName($value)) {
          $output .= self::$module->displayError(self::$module->l('Update failure: ') . $text);
        } else {
          if ($value !== Configuration::get($confVal['field_upper'])) {
            Configuration::updateValue($confVal['field_upper'], $value);
            $output .= self::$module->displayConfirmation(self::$module->l('Update succesful: ') . $text);
          }
        }
      }
    }
    return $output;
  }

  /**
   * Method returns an array with active attributes ids
   * @param array $attributesGroups
   * @param array $confValues array of arrays in "confValueFormat"
   * @return array
   */
  private static function getActiveAttributes($attributesGroups, $confValues)
  {
    foreach ($attributesGroups as $group) {
      $attributes[$group['id_attribute_group']] = Configuration::get($confValues[$group['id_attribute_group']]['is_active']['field_upper']);
    }
    $attributes = array_filter($attributes);
    return array_keys($attributes);
  }

  /**
   * Method returns an array of all group attributes divided in half 
   * @param array $groupId
   * @return array
   */
  private static function getSlicedAtrributesArray($groupId): array
  {
    $attributes = ScsHelper::getAttributes($groupId, self::$module->contextLangId);
    $middle = (int)floor(count($attributes) / 2);
    $sliced['start'] = array_slice($attributes, 0, $middle, true);
    $sliced['end'] = array_slice($attributes, $middle, null, true);
    return $sliced;
  }

  private static function groupsForm($elements): array
  {
    $fields_form['form'] = array(
      'legend' => array(
        'title' => self::$module->l('Select attribute groups for size table use'),
        'icon' => 'icon-cogs'
      ),
      'input' => array_merge(
        array(array('type' => 'free', 'name' => 'attribute_use_description', 'col' => 3, 'offset' => 0),),
        $elements
      ),
      'submit' => array(
        'title' => self::$module->l('Save'),
        'class' => 'btn btn-default pull-right'
      )
    );
    return $fields_form;
  }

  private static function dimensionsForm($elements): array
  {
    $fields_form['form'] = array(
      'legend' => array(
        'title' => self::$module->l('Choose basis of dimensions'),
        'icon' => 'icon-cogs'
      ),
      'input' => $elements,
      'submit' => array(
        'title' => self::$module->l('Save'),
        'class' => 'btn btn-default pull-right'
      )
    );
    return $fields_form;
  }

  private static function elementGroupRadio($groupConfValue): array
  {
    return [
      'type' => 'switch',
      'label' => $groupConfValue['group_name'],
      'name' => $groupConfValue['field_lower'],
      'hint' => self::$module->l('Click "Yes" to set this attribute group for size table use'),
      'is_bool' => true,
      'desc' =>  self::$module->l('Attribute sizes') . ': ' . implode(', ', ScsHelper::getAttributes($groupConfValue['group_id'], self::$module->contextLangId)),
      'values' => array(
        array(
          'id' => 'active_on',
          'value' => 1,
          'label' => self::$module->l('Enabled')
        ),
        array(
          'id' => 'active_off',
          'value' => 0,
          'label' => self::$module->l('Disabled')
        )
      ),
    ];
  }

  private static function elementDimensionSelect($confValue, $sliced): array
  {
    return [
      'type' => 'select',
      'label' => ucfirst($confValue['label']),
      'name' => $confValue['field_lower'],
      'desc' =>  self::$module->l('Basic dimension for group: ') . $confValue['group_name'],
      'options' => [
        'query' => self::getGroupAttributesOptions($sliced[$confValue['field_name']]),
        'id' => 'id_option',
        'name' => 'name'
      ],
    ];
  }

  private static function getGroupAttributesOptions($attributes)
  {
    foreach ($attributes as $key => $value) {
      $arr[] = [
        'id_option' => $key,
        'name' => $value,
      ];
    }
    return  $arr;
  }

  private static function getAttributesDescription(): array
  {
    $desc = '<div class="alert alert-info">
                   <ul>
                     <li>' . self::$module->l('Poniższe grupy atrybutów mogą zostać użyte do wygenerowania tabeli rozmiarów.') . '</li>
                     <li>' . self::$module->l('Zwróć uwagę na to czy poszczególne rozmiary są ułożone od najmniejszego do największego.') . '</li>
                     <li>' . self::$module->l('Ustawienie kolejności atrybutów można zmieniać za pomocą mechanizmu pozycji atrybutów') . '</li>
                 </ul>
                 </div>';
    return array('attribute_use_description' => $desc);
  }
}
