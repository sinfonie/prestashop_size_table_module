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

  public static function init(SinClothesSizing $module)
  {
    if (self::$module == false) {
      self::$module = $module;
    }

    $instance = new self;
    $instance->attributesGroups = ScsHelper::getGroupsAttributes(self::$module->contextLanguageID);
    $instance->index = self::$module->contextAdminLink . '&configure=' . self::$module->name . '&tab_module=' . self::$module->tab . '&module_name=' . self::$module->name;
    $instance->token = Tools::getAdminTokenLite('AdminModules');
    $instance->display = '';
    return $instance;
  }

  public function getHTML(): string
  {
    $this->checkModelSubmit();
    $models = $this->getModelsList();
    if (!empty($this->attributesGroups)) {
      if (Tools::isSubmit('add_new_model_submit')) {
        $view = $this->displayCreateForm();
      } elseif (Tools::isSubmit('update_model')) {
        $view = $this->displayUpdateForm();
      } else {
        $view = $this->displayAddForm();
      }
    } else {
      $view = 'there is no attributes';
    }
    return $this->display . $view . $models;
  }

  ### model submit ###

  private function checkModelSubmit(): void
  {
    if (Tools::isSubmit('create_new_model_submit')) {
      $this->submitNewModel();
    } elseif (Tools::isSubmit('update_model_submit')) {
      $this->submitUpdateModel();
    }
  }

  private function submitNewModel(): void
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
      $this->validateAndDisplay($model, 'save');
    }
  }

  private function submitUpdateModel(): void
  {
    $model = new ScsDbModels;
    $model = $model->getModel((int)Tools::getValue('id'));
    $model->name = Tools::getValue('name');
    $model->active = Tools::getValue('active');
    $model->properties = serialize(ScsHelper::getLangProperties());
    $this->validateAndDisplay($model, 'update');
  }

  private function validateAndDisplay(ScsDbModels $model, string $text): void
  {
    if ($this->isUnique($model, $text)) {
      if ($model->validateFields()) {
        $model->$text();
        $this->display = self::$module->displayConfirmation(self::$module->l(ucfirst($text) . ' success', 'ScsForm'));
      } else {
        $this->display = self::$module->displayError(self::$module->l(ucfirst($text) . ' failure', 'ScsForm'));
      }
    } else {
      $this->display = self::$module->displayError(self::$module->l('This model already exists', 'ScsForm'));
    }
  }

  private function isUnique($model, $text): bool
  {
    if ($text == 'save') {
      $models =  new ScsDbModels;
      $models = array_filter($models->getModels(), function ($m) use ($model) {
        unset($model->id);
        unset($m->id);
        return ($model == $m);
      });
      if (!empty($models)) return false;
    }
    return true;
  }

  ### basic helpers ###

  private function getFormHelper()
  {
    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
    $helper = new HelperForm();
    $helper->module = self::$module;
    $helper->name_controller = self::$module->name;
    $helper->token = $this->token;
    $helper->currentIndex = $this->index;
    $helper->default_form_language = $default_lang;
    $helper->allow_employee_form_lang = $default_lang;
    $helper->title = self::$module->displayName;
    return $helper;
  }

  private function getModelsList(): string
  {
    $models = $this->prepareModels();
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
    $helper->token = $this->token;
    $helper->currentIndex = $this->index;
    return $helper->generateList($models, $this->getListColumns());
  }



  ### Add model process ###

  private function displayAddForm(): string
  {
    $form = $this->addModelForm();
    $helper = $this->getFormHelper();
    $helper->submit_action = 'add_new_model_submit';
    $helper->tpl_vars = [
      'fields_value' => $form['values']
    ];
    return $helper->generateForm($form['fields']);
  }

  private function addModelForm(): array
  {
    $form = [];
    $form['values']['attr_group_id'] = null;
    $form['fields'][0] = $this->addModelFields();
    return $form;
  }

  private function addModelFields(): array
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
            'query' => $this->groupSelect(),
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

  private function groupSelect(): array
  {
    $arr = array_map(function ($group) {
      return [
        'id_option' => $group['id_attribute_group'],
        'name' => $group['name'],
      ];
    }, $this->attributesGroups);
    return  $arr;
  }

  ### Create model process ###

  private function displayCreateForm(): string
  {
    $form = $this->createModelForm();
    $helper = $this->getFormHelper();
    $helper->submit_action = 'create_new_model_submit';
    $helper->tpl_vars = [
      'languages' => self::$module->contextLanguages,
      'show_cancel_button' => true,
      'back_url' => $this->index . '&token=' . Tools::getAdminTokenLite('AdminModules'),
    ];
    return $helper->generateForm($form['fields']);
  }

  private function createModelForm(): array
  {
    $noProperties = intval(Tools::getValue('no_properties'));
    $noProperties = ($noProperties <= 1) ? 1 : $noProperties;
    $formSettings = [
      'attr_group_id' => Tools::getValue('attr_group_id'),
      'group_name' => $this->attributesGroups[Tools::getValue('attr_group_id')]['name'],
      'no_properties' => $noProperties,
    ];
    $form = [];
    $form['fields'][0] = $this->createModelFields($formSettings);
    return $form;
  }

  private function createModelFields($formSettings): array
  {
    $sliced = $this->getSlicedAtrributesArray($formSettings['attr_group_id']);
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
            'query' => $this->dimensionSelect($sliced['start']),
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
            'query' => $this->dimensionSelect($sliced['end']),
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
    $form['form']['input'] = array_merge($form['form']['input'], $this->getTextFields($formSettings['no_properties']), $submit);

    return  $form;
  }

  private function getSlicedAtrributesArray($groupId): array
  {
    $attributes = ScsHelper::getAttributes($groupId, self::$module->contextLanguageID);

    $middle = (int)floor(count($attributes) / 2);
    $sliced['start'] = array_slice($attributes, 0, $middle, true);
    $sliced['end'] = array_slice($attributes, $middle, null, true);
    return $sliced;
  }

  private function dimensionSelect(array $dimensions): array
  {
    array_walk($dimensions, function (&$dimension, $key) {
      $dimension = [
        'id_option' => $key,
        'name' => $dimension,
      ];
    });
    return $dimensions;
  }

  private function getTextFields($noTextInputs): array
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

  ### Update model process ###

  private function displayUpdateForm(): string
  {
    $form = $this->updateModelForm();
    $helper = $this->getFormHelper();
    $helper->submit_action = 'update_model_submit';
    $helper->tpl_vars = [
      'fields_value' => $form['values'],
      'languages' => self::$module->contextLanguages,
      'show_cancel_button' => true,
      'back_url' => $this->index . '&token=' . Tools::getAdminTokenLite('AdminModules'),
    ];
    return $helper->generateForm($form['fields']);
  }

  private function updateModelForm(): array
  {
    $form = [];
    $form['values'] = $this->getUpdateFormValues();
    $form['fields'][0] = $this->updateModelFields($form['values']);
    return $form;
  }

  private function getUpdateFormValues(): array
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
      'group_name' => $this->attributesGroups[$model->attr_group_id]['name'],
    ];
    foreach ($properties as $propID => $property) {
      $values['property_' . $propID] = $property;
    }
    return $values;
  }

  private function updateModelFields($values): array
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
    $form['form']['input'] = array_merge($form['form']['input'], $this->getTextFields($values['no_properties']), $hidden);

    return  $form;
  }

  ### model list ###

  private function prepareModels(): array
  {
    return array_map(function ($item) {
      return [
        'id' => $item->id,
        'name' => $item->name,
        'attribute' => $this->attributesGroups[$item->attr_group_id]['name'],
        'used_in' => 0 . ' ' . self::$module->l('products', 'ScsForm'),
      ];
    }, ScsDbModels::getModels());
  }

  private function getListColumns(): array
  {
    return [
      'name' => ['title' => self::$module->l('Name', 'ScsForm'), 'type' => 'text', 'orderby' => false],
      'attribute' => ['title' => self::$module->l('Attributes group', 'ScsForm'), 'type' => 'text', 'orderby' => false],
      'used_in' => ['title' => self::$module->l('Used in', 'ScsForm'), 'type' => 'text', 'orderby' => false],
    ];
  }
}
