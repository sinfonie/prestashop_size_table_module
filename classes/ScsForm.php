<?php

/**
 * @author Maciej Rumiński <ruminski.maciej@gmail.com>
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

class ScsForm
{
  private static $module = false;
  public static $attributesGroups = [];

  public static function init(SinClothesSizing $module)
  {
    if (self::$module == false) {
      self::$module = $module;
    }

    self::$attributesGroups = ScsHelper::getGroupsAttributes(self::$module->languageID);

    return self::$module;
  }

  public static function submitModel()
  {
    var_dump($_POST);

    $display = '';

    if (Tools::isSubmit('create_new_model_submit')) {
      $display = self::submitNewModel();
    } elseif (Tools::isSubmit('update_model_submit')) {
      $display = self::submitUpdateModel();
    }

    return $display;
  }

  private static function submitUpdateModel()
  {
    $display = '';
    $model = new ScsDb;
    $model->id = Tools::getValue('id');
    $model->name = Tools::getValue('name');
    $model->active = Tools::getValue('active');
    $model->properties = serialize(ScsHelper::getLangProperties());

    if ($model->validateFields()) {
      $model->save();
      $display = self::$module->displayConfirmation(self::$module->l('Model updated', 'ScsForm'));
    } else {
      $display = self::$module->displayError(self::$module->l('Model update failed', 'ScsForm'));
    }
    return $display;
  }


  private static function submitNewModel()
  {
    $display = '';
    if (Tools::isSubmit('create_new_model_submit')) {
      $model = new ScsDb;
      foreach (array_keys(ScsDb::$definition['fields']) as $field) {
        if ($field == 'properties') continue;
        if ($field == 'active') continue;
        $newModel->$field = Tools::getValue($field);
      }
      $model->properties = serialize(ScsHelper::getLangProperties());
      $model->active = true;
      if ($model->validateFields()) {
        $model->save();
        $display = self::$module->displayConfirmation(self::$module->l('New model created', 'ScsForm'));
      } else {
        $display = self::$module->displayError(self::$module->l('New model creation failed', 'ScsForm'));
      }
    }
    return $display;
  }

  public static function addModelForm()
  {
    $form = [];
    $form['fields'][0] = self::addModelFields();
    $form['values']['attr_group_id'] = null;
    return $form;
  }

  public static function createModelForm()
  {
    $noProperties = intval(Tools::getValue('no_properties'));
    $noProperties = ($noProperties <= 1) ? 1 : $noProperties;
    $formSettings = [
      'attr_group_id' => Tools::getValue('attr_group_id'),
      'group_name' => self::$attributesGroups[Tools::getValue('attr_group_id')]['name'],
      'no_properties' => $noProperties,
    ];
    $form = [];
    $form['fields'][0] = self::createModelFields($formSettings);
    return $form;
  }


  private static function addModelFields()
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
            'query' => self::groupSelect(),
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
          'html_content' => '<button type="submit" value="1" id="submit-add-model" class="btn btn-primary">
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
        <button type="submit" value="1" id="submit-create-model" class="btn btn-primary">
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

  private static function dimensionSelect($dimensions)
  {
    array_walk($dimensions, function (&$dimension, $key) {
      $dimension = [
        'id_option' => $key,
        'name' => $dimension,
      ];
    });
    return  $dimensions;
  }

  private static function groupSelect()
  {
    $arr = array_map(function ($group) {
      return [
        'id_option' => $group['id_attribute_group'],
        'name' => $group['name'],
      ];
    }, self::$attributesGroups);
    return  $arr;
  }


  private static function getSlicedAtrributesArray($groupId): array
  {
    $attributes = ScsHelper::getAttributes($groupId, self::$module->languageID);

    $middle = (int)floor(count($attributes) / 2);
    $sliced['start'] = array_slice($attributes, 0, $middle, true);
    $sliced['end'] = array_slice($attributes, $middle, null, true);
    return $sliced;
  }

  ################################################################

  public static function getModels()
  {
    $models = self::prepareModels();
    $helper = new HelperList();
    $helper->title = self::$module->l('Model list', 'ScsForm');
    $helper->table = '_model';
    $helper->no_link = true;
    $helper->simple_header = false;
    $helper->shopLinkType = '';
    $helper->identifier = 'id';
    $helper->actions = array('edit');
    $helper->listTotal = count($models);
    $helper->tpl_vars = array('show_filters' => false);
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = self::$module->adminLink
      . '&configure=' . self::$module->name . '&tab_module=' . self::$module->tab . '&module_name=' . self::$module->name;
    return $helper->generateList($models, self::getModelList());
  }

  private static function prepareModels()
  {
    return array_map(function ($item) {
      return [
        'id' => $item->id,
        'name' => $item->name,
        'attribute' => self::$attributesGroups[$item->attr_group_id]['name'],
        'used_in' => 0 . ' ' . self::$module->l('products', 'ScsForm'),
      ];
    }, ScsDb::dbModels());
  }

  private static function getModelList()
  {
    return array(
      'name' => array('title' => self::$module->l('Name', 'ScsForm'), 'type' => 'text', 'orderby' => false),
      'attribute' => array('title' => self::$module->l('Attributes group', 'ScsForm'), 'type' => 'text', 'orderby' => false),
      'used_in' => array('title' => self::$module->l('Used in', 'ScsForm'), 'type' => 'text', 'orderby' => false),
    );
  }


  public static function updateModelForm()
  {
    $id = Tools::getValue('id');
    $model = ScsDb::dbModel($id);
    $properties = unserialize($model->properties);
    $noProperties = count($properties);
    $form['values'] = [
      'id' => $model->id,
      'attr_group_id' => $model->attr_group_id,
      'dim_start' => $model->dim_start,
      'dim_end' => $model->dim_end,
      'name' => $model->name,
      'properties' => $properties,
      'no_properties' => $noProperties,
      'active' => $model->active,
      'group_name' => self::$attributesGroups[$model->attr_group_id]['name'],
    ];
    foreach ($properties as $propID => $property) {
      $form['values']['property_' . $propID] = $property;
    }
    $form['fields'][0] = self::updateModelFields($form['values']);
    return $form;
  }

  private static function updateModelFields($values)
  {
    $form['form'] = [
      'legend' => [
        'title' => self::$module->l('Update model for: ', 'ScsForm') . $values['group_name'],
        'icon' => 'icon-cogs'
      ],
      'input' => [
        [
          'label' => self::$module->l('Start dimension', 'ScsForm'),
          'type'  => 'html',
          'html_content' => '<div class="scs__row">' . $values['dim_start'] . '</div>',
        ],
        [
          'label' => self::$module->l('End dimension', 'ScsForm'),
          'type'  => 'html',
          'html_content' => '<div class="scs__row">' . $values['dim_end'] . '</div>',
        ],
        [
          'type' => 'text',
          'label' => self::$module->l('Model name', 'ScsForm'),
          'name' => 'name',
          'size' => 64,
        ],
        [
          'type' => 'switch',
          'label' => self::$module->l('Active', 'ScsForm'),
          'name' => 'active',
          'values' => [
            [
              'id'    => 'active_on',
              'value' => 1,
            ],
            [
              'id'    => 'active_off',
              'value' => 0,
            ],
          ]
        ],
        [
          'type'  => 'html',
          'html_content' => '<input type="hidden" value=" id="attr_group_id" name="attr_group_id">',
        ],
      ],
    ];
    $submit = [
      [
        'type'  => 'html',
        'html_content' => '<button type="submit" id="submit-update-model" class="btn btn-primary">' . self::$module->l('Update model', 'ScsForm') . '</button>',
      ],
      [
        'type'  => 'html',
        'html_content' => '<input type="hidden" id="no_properties" value="' . $values['no_properties'] . '"  name="no_properties">'
      ],
      [
        'type'  => 'html',
        'html_content' => '<input type="hidden" id="id" value="' . $values['id'] . '"  name="id">'
      ],
    ];
    $form['form']['input'] = array_merge($form['form']['input'], self::getTextFields($values['no_properties']), $submit);

    return  $form;
  }
}
