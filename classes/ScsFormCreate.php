<?php

/**
 * ScsForm class is responsible for managing and creating a configuration form for the module
 * @author Maciej Rumiński <ruminski.maciej@gmail.com>
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

class ScsFormCreate
{
  protected static $module = false;

  public static function init(SinClothesSizing $module)
  {
    if (self::$module == false) {
      self::$module = $module;
    }
    return self::$module;
  }

  public static function getValues($attributesGroups)
  {
    $form = [];
    $form['fields'][0] = self::getFormFields($attributesGroups);
    $form['values']['new_attr_group_id'] = null;
    return $form;
  }

  public static function submitNewModel()
  {
    var_dump(Tools::getValue('new_attr_group_id'));
  }

  public static function getFormFields($attributesGroups)
  {
    $form['form'] = [
      'legend' => [
        'title' => self::$module->l('Select attribute groups for size table use'),
        'icon' => 'icon-cogs'
      ],
      'input' => [
        [
          'type' => 'select',
          'label' => self::$module->l('Select and add new model'),
          'name' => 'new_attr_group_id',
          'options' => [
            'query' => self::groupSelect($attributesGroups),
            'id' => 'id_option',
            'name' => 'name',
          ],
        ],
        [
          'type'  => 'html',
          'label' => self::$module->l('Set number of properties'),
          'html_content' => '<input type="number" id="no_properties" name="no_properties">'
        ],
        [
          'type'  => 'html',
          'html_content' => '<button type="submit" value="1" id="configuration_form_submit_btn" name="sinclothessizing_submit" class="btn btn-primary">
							' . self::$module->l('Add new model') . '
						</button>'
        ],
      ],
    ];
    return $form;
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

  public static function getAddForm($formSettings)
  {
    $form = [];
    $form['fields'][0] = self::getAddFormFields($formSettings);
    $form['values'] = [
      'attr_group_id' => $groupId,
    ];
    return $form;
  }

  private static function getAddFormFields($formSettings)
  {
    $sliced = self::getSlicedAtrributesArray($formSettings['group_id']);
    $form['form'] = [
      'legend' => [
        'title' => self::$module->l('Add new dimension model for: ') . $formSettings['group_name'],
        'icon' => 'icon-cogs'
      ],
      'input' => [
        [
          'type' => 'select',
          'label' => self::$module->l('Start dimension'),
          'name' => 'dim_start',
          'options' => [
            'query' => self::dimensionSelect($sliced['start']),
            'id' => 'id_option',
            'name' => 'name',
          ],
        ],
        [
          'type' => 'select',
          'label' => self::$module->l('End dimension'),
          'name' => 'dim_end',
          'options' => [
            'query' => self::dimensionSelect($sliced['end']),
            'id' => 'id_option',
            'name' => 'name',
          ],
        ],
        [
          'type'  => 'html',
          'html_content' => '<input type="hidden" value="' . $formSettings['number_of_properties'] . '" id="no_properties" name="no_properties"">',
        ]
      ],
    ];
    $submit = [
      [
        'type'  => 'html',
        'html_content' => '<button type="submit" value="1" id="configuration_form_submit_btn" name="sinclothessizing_submit" class="btn btn-primary">
      ' . self::$module->l('Save new model') . '</button>'
      ]
    ];
    var_dump($formSettings['number_of_properties']);
    $form['form']['input'] = array_merge($form['form']['input'], self::getTextFields($formSettings['number_of_properties']), $submit);

    return  $form;
  }

  private static function getTextFields($noTextInputs)
  {
    for ($i = 1; $i <= $noTextInputs; $i++) {
      $output[] = [
        'type'  => 'text',
        'label' => self::$module->l('Name of property: ') . $i,
        'name'  => 'property_' . $i,
        'lang'  => true,
      ];
    }
    return $output;
  }

  private static function dimensionSelect($attributesGroups)
  {
    foreach ($attributesGroups as $key => $name) {
      $arr[] = [
        'id_option' => $key,
        'name' => $name,
      ];
    }
    return  $arr;
  }

  private static function groupSelect($attributesGroups)
  {
    foreach ($attributesGroups as $group) {
      $arr[] = [
        'id_option' => $group['id_attribute_group'],
        'name' => $group['name'],
      ];
    }
    return  $arr;
  }


  private static function getSlicedAtrributesArray($groupId): array
  {
    $attributes = ScsHelper::getAttributes($groupId, self::$module->contextLangId);
    $middle = (int)floor(count($attributes) / 2);
    $sliced['start'] = array_slice($attributes, 0, $middle, true);
    $sliced['end'] = array_slice($attributes, $middle, null, true);
    return $sliced;
  }
}
