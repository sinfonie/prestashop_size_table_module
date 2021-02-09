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
  public static $attributesGroups;
  public static $index;
  public static $display;

  public static function init(SinClothesSizing $module)
  {
    if (self::$module == false) {
      self::$module = $module;
    }
    self::$attributesGroups = ScsHelper::getGroupsAttributes(self::$module->languageID);
    self::$index = self::$module->adminLink . '&configure=' . self::$module->name . '&tab_module=' . self::$module->tab . '&module_name=' . self::$module->name;
    self::$display = '';
  }

  public static function checkModelSubmit(): void
  {
    if (Tools::isSubmit('create_new_model_submit')) {
      self::submitNewModel();
    } elseif (Tools::isSubmit('update_model_submit')) {
      self::submitUpdateModel();
    }
  }

  public static function addModelForm(): array
  {
    $form = [];
    $form['values']['attr_group_id'] = null;
    $form['fields'][0] = self::addModelFields();
    return $form;
  }

  public static function createModelForm(): array
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

  public static function updateModelForm(): array
  {
    $form = [];
    $form['values'] = self::getUpdateFormValues();
    $form['fields'][0] = self::updateModelFields($form['values']);
    return $form;
  }

  public static function getModels(): string
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
    $helper->currentIndex = self::$index;
    return $helper->generateList($models, self::getModelList());
  }

  private static function addModelFields(): array
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

  private static function createModelFields($formSettings): array
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
          'required' => true,
          'options' => [
            'query' => self::dimensionSelect($sliced['start']),
            'id' => 'id_option',
            'name' => 'name',
          ],
        ],
        [
          'type' => 'select',
          'label' => self::$module->l('End dimension', 'ScsForm'),
          'name' => 'dim_end',
          'required' => true,
          'options' => [
            'query' => self::dimensionSelect($sliced['end']),
            'id' => 'id_option',
            'name' => 'name',
          ],
        ],
        [
          'type' => 'text',
          'label' => self::$module->l('Model name', 'ScsForm'),
          'name' => 'name',
          'required' => true,
          'size' => 64,
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
      'buttons' => [],
    ];
    $submit = [
      [
        'type'  => 'html',
        'html_content' => '
        <button type="submit" value="save" id="submit-create-model" class="btn btn-primary">
      ' . self::$module->l('Save new model', 'ScsForm') . '</button>',
      ]
    ];
    $form['form']['input'] = array_merge($form['form']['input'], self::getTextFields($formSettings['no_properties']), $submit);

    return  $form;
  }

  private static function getUpdateFormValues(): array
  {
    $id = Tools::getValue('id');
    $model = ScsDbModels::getModel($id);
    $properties = unserialize($model->properties);
    $noProperties = count($properties);
    $values = [
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
      $values['property_' . $propID] = $property;
    }
    return $values;
  }

  private static function updateModelFields($values): array
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
      'buttons' => [
        'submit' => [
          'title' => self::$module->l('Save'),
          'name' => 'save',
          'type' => 'submit',
          'class' => 'btn btn-default pull-right',
          'icon' => 'process-icon-save',
        ],
      ],
    ];
    $hidden = [
      [
        'type'  => 'html',
        'html_content' => '<input type="hidden" id="no_properties" value="' . $values['no_properties'] . '"  name="no_properties">'
      ],
      [
        'type'  => 'html',
        'html_content' => '<input type="hidden" id="id" value="' . $values['id'] . '"  name="id">'
      ],
    ];
    $form['form']['input'] = array_merge($form['form']['input'], self::getTextFields($values['no_properties']), $hidden);

    return  $form;
  }

  private static function getTextFields($noTextInputs): array
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

  private static function dimensionSelect(array $dimensions): array
  {
    array_walk($dimensions, function (&$dimension, $key) {
      $dimension = [
        'id_option' => $key,
        'name' => $dimension,
      ];
    });
    return $dimensions;
  }

  private static function groupSelect(): array
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


  private static function prepareModels()
  {
    return array_map(function ($item) {
      return [
        'id' => $item->id,
        'name' => $item->name,
        'attribute' => self::$attributesGroups[$item->attr_group_id]['name'],
        'used_in' => 0 . ' ' . self::$module->l('products', 'ScsForm'),
      ];
    }, ScsDbModels::getModels());
  }

  private static function getModelList()
  {
    return array(
      'name' => array('title' => self::$module->l('Name', 'ScsForm'), 'type' => 'text', 'orderby' => false),
      'attribute' => array('title' => self::$module->l('Attributes group', 'ScsForm'), 'type' => 'text', 'orderby' => false),
      'used_in' => array('title' => self::$module->l('Used in', 'ScsForm'), 'type' => 'text', 'orderby' => false),
    );
  }


  private static function submitUpdateModel(): void
  {
    $model = new ScsDbModels;
    $model = $model->getModel((int)Tools::getValue('id'));
    $model->name = Tools::getValue('name');
    $model->active = Tools::getValue('active');
    $model->properties = serialize(ScsHelper::getLangProperties());
    self::validateAndDisplay($model, 'update');
  }

  private static function submitNewModel(): void
  {
    if (Tools::isSubmit('create_new_model_submit')) {
      $model = new ScsDbModels;
      foreach (array_keys(ScsDbModels::$definition['fields']) as $field) {
        if ($field == 'properties') continue;
        if ($field == 'active') continue;
        $model->$field = Tools::getValue($field);
      }
      $model->active = true;
      $model->properties = serialize(ScsHelper::getLangProperties());
      self::validateAndDisplay($model, 'save');
    }
  }

  private static function validateAndDisplay(ScsDbModels $model, string $text): void
  {
    if ($model->validateFields()) {
      $model->$text();
      self::$display = self::$module->displayConfirmation(self::$module->l(ucfirst($text) . ' success', 'ScsForm'));
    } else {
      self::$display = self::$module->displayError(self::$module->l(ucfirst($text) . ' failure', 'ScsForm'));
    }
  }
}
