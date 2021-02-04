<?php

/**
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

  public static function submitNewModel()
  {
    $display = '';
    if (Tools::isSubmit('create_new_model_submit')) {
      $newModel = new ScsDb;
      foreach (array_keys(ScsDb::$definition['fields']) as $field) {
        if ($field == 'properties') continue;
        $newModel->$field = Tools::getValue($field);
      }
      $newModel->properties = serialize(ScsHelper::getLangProperties());
      $newModel->active = true;
      if ($newModel->validateFields()) {
        $newModel->save();
        $display = self::$module->displayConfirmation(self::$module->l('New model created', 'ScsForm'));
      } else {
        $display = self::$module->displayError(self::$module->l('New model creation failed', 'ScsForm'));
      }
    }
    return $display;
  }

  public static function addModelForm($attributesGroups)
  {
    $form = [];
    $form['fields'][0] = self::addModelFields($attributesGroups);
    $form['values']['attr_group_id'] = null;
    return $form;
  }

  public static function createModelForm($attributesGroups)
  {
    $noProperties = intval(Tools::getValue('no_properties'));
    $noProperties = ($noProperties <= 1) ? 1 : $noProperties;
    $formSettings = [
      'attr_group_id' => Tools::getValue('attr_group_id'),
      'group_name' => $attributesGroups[Tools::getValue('attr_group_id')]['name'],
      'no_properties' => $noProperties,
    ];
    $form = [];
    $form['fields'][0] = self::createModelFields($formSettings);
    return $form;
  }


  private static function addModelFields($attributesGroups)
  {
    $form['form'] = [
      'legend' => [
        'title' => self::$module->l('Select attribute groups for size table use', 'ScsForm'),
        'icon' => 'icon-cogs'
      ],
      'input' => [
        [
          'type' => 'select',
          'label' => self::$module->l('Select and add new model', 'ScsForm'),
          'name' => 'attr_group_id',
          'options' => [
            'query' => self::groupSelect($attributesGroups),
            'id' => 'id_option',
            'name' => 'name',
          ],
        ],
        [
          'type'  => 'html',
          'label' => self::$module->l('Set number of properties', 'ScsForm'),
          'html_content' => '<input type="number" min="1" value="1" id="no_properties" name="no_properties">'
        ],
        [
          'type'  => 'html',
          'html_content' => '<button type="submit" value="1" id="configuration_form_submit_btn" class="btn btn-primary">
							' . self::$module->l('Add new model', 'ScsForm') . '
						</button>'
        ],
      ],
    ];
    return $form;
  }

  private static function createModelFields($formSettings)
  {
    $sliced = self::getSlicedAtrributesArray($formSettings['attr_group_id']);
    $form['form'] = [
      'legend' => [
        'title' => self::$module->l('Add new dimension model for: ', 'ScsForm') . $formSettings['group_name'],
        'icon' => 'icon-cogs'
      ],
      'input' => [
        [
          'type' => 'select',
          'label' => self::$module->l('Start dimension', 'ScsForm'),
          'name' => 'dim_start',
          'options' => [
            'query' => self::dimensionSelect($sliced['start']),
            'id' => 'id_option',
            'name' => 'name',
          ],
          'required' => true,
        ],
        [
          'type' => 'select',
          'label' => self::$module->l('End dimension', 'ScsForm'),
          'name' => 'dim_end',
          'options' => [
            'query' => self::dimensionSelect($sliced['end']),
            'id' => 'id_option',
            'name' => 'name',
          ],
          'required' => true,
        ],
        [
          'type' => 'text',
          'label' => self::$module->l('Model name', 'ScsForm'),
          'name' => 'name',
          'size' => 64,
          'required' => true,
        ],
        [
          'type'  => 'html',
          'html_content' => '<input type="hidden" value="' . $formSettings['attr_group_id'] . '" id="attr_group_id" name="attr_group_id">',
        ],
        [
          'type'  => 'html',
          'html_content' => '<input type="hidden"  value="' . $formSettings['no_properties'] . '"  id="no_properties" name="no_properties">'
        ],
      ],
    ];
    $submit = [
      [
        'type'  => 'html',
        'html_content' => '
        <button type="submit" value="1" id="configuration_form_submit_btn" class="btn btn-primary">
      ' . self::$module->l('Save new model', 'ScsForm') . '</button>',
      ]
    ];
    $form['form']['input'] = array_merge($form['form']['input'], self::getTextFields($formSettings['no_properties']), $submit);

    return  $form;
  }

  private static function getTextFields($noTextInputs)
  {
    for ($i = 1; $i <= $noTextInputs; $i++) {
      $output[] = [
        'type'  => 'text',
        'label' => self::$module->l('Name of property: ', 'ScsForm') . $i,
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







  public static function getModels()
  {

    echo '<pre>';
    //var_dump(ScsDb::dbModels());
    echo '</pre>';


    $models = ScsDb::dbModels();

    $models = array_map(function ($item) {
      return [
        'id' => $item->id,
        'name' => $item->name,
      ];
    }, $models);

    $helper = new HelperList();

    $helper->title = self::$module->l('Model list', 'ScsForm');
    $helper->table = self::$module->name;
    $helper->no_link = true;
    $helper->shopLinkType = '';
    $helper->identifier = 'id';
    $helper->actions = array('edit', 'delete');

    $values = $models;
    $helper->listTotal = count($values);
    $helper->tpl_vars = array('show_filters' => false);
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    return $helper->generateList($values, self::getTasksList());
  }

  public static function getTasksList()
  {
    return array(
      'id' => array('title' => 'id', 'type' => 'text', 'orderby' => false),
      'name' => array('title' => 'name', 'type' => 'text', 'orderby' => false),
    );
  }
}
