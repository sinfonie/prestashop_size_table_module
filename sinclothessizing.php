<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__) . '/classes/ScsHelper.php');
require_once(dirname(__FILE__) . '/classes/ScsForm.php');
require_once(dirname(__FILE__) . '/classes/ScsDb.php');

class SinClothesSizing extends Module
{
    public $attributesGroups;
    public $modPrefix = 'scs_';

    public function __construct()
    {
        $this->name = 'sinclothessizing';
        $this->tab = 'front_office_features';
        $this->version = '1.0.5';
        $this->author = 'sinfonie';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->id_product = (int) Tools::getValue('id_product');
        $this->displayName = $this->l('Clothes sizing');
        $this->description = $this->l('Allows to add a clothes sizing');
        $this->arraySizes = array('xs' => 1, 's' => 2, 'm' => 3, 'l' => 4, 'xl' => 5, 'xxl' => 6);
        $this->baseSizesNames = array('bust_s', 'bust_xl', 'waist_s', 'waist_xl', 'hips_s', 'hips_xl', 'length_s', 'length_xl');
        $this->dimentionsNames = array('bust', 'waist', 'hips', 'length');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->realSizes = $this->realSizes();
        $this->getSizes = $this->getSizes();

        $this->getContext();
        require_once dirname(__FILE__) . '/classes/SizeObject.php';
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        include dirname(__FILE__) . '/sql/install.php';

        if (
            !parent::install() ||
            !$this->registerHook('header') ||
            !$this->registerHook('backOfficeHeader') ||
            !$this->registerHook('displayAdminProductsExtra') ||
            !$this->registerHook('displayRightColumnProduct') ||
            !$this->registerHook('actionObjectProductDeleteAfter') ||
            !$this->registerHook('actionProductUpdate')
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        ScsForm::init($this);

        return '<div class="scs__admin col-lg-7">' . $this->getForms() . '</div>';
    }

    private function getForms(): string
    {
        $display = ScsForm::submitModel();
        $models = ScsForm::getModels();
        if (!empty(ScsForm::$attributesGroups)) {
            if (Tools::isSubmit('add_new_model_submit')) {
                //create new model form
                $form = ScsForm::createModelForm();
                $view = $this->displayCreateForm($form);
            } elseif (Tools::isSubmit('update_model')) {
                //update model from list
                $form = ScsForm::updateModelForm();
                $view = $this->displayUpdateForm($form);
            } else {
                //add new model form
                $form = ScsForm::addModelForm();
                $view = $this->displayAddForm($form);
            }
        } else {
            $view = 'the is no attributes';
        }
        unset($_POST);
        return $display . $view . $models;
    }

    private function getContext()
    {
        $this->languageID = $this->context->language->id;
        $this->adminLink  = $this->context->link->getAdminLink('AdminModules', false);
    }

    private function displayUpdateForm($form): string
    {
        $helper = $this->getFormHelper();
        $helper->submit_action = 'update_model_submit';
        $helper->tpl_vars = [
            'languages' => $this->context->controller->getLanguages(),
            'fields_value' => $form['values'],
        ];
        return $helper->generateForm($form['fields']);
    }

    private function displayAddForm($form): string
    {
        $helper = $this->getFormHelper();
        $helper->submit_action = 'add_new_model_submit';
        $helper->tpl_vars = ['fields_value' => $form['values']];
        return $helper->generateForm($form['fields']);
    }

    private function displayCreateForm($form): string
    {
        $helper = $this->getFormHelper();
        $helper->submit_action = 'create_new_model_submit';
        $helper->tpl_vars = [
            'languages' => $this->context->controller->getLanguages(),
        ];
        return $helper->generateForm($form['fields']);
    }

    private function getFormHelper()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        return $helper;
    }



    /////////////////////////////////////////////


    private function realSizes()
    {
        $product = new Product($this->id_product);
        $attr = $product->getAttributeCombinations(Context::getContext()->language->id);
        $attrs = [];
        foreach ($attr as $key => $val) {
            if ($val['id_attribute_group'] === '1') {
                $attrs[$val['id_attribute']] = $val['id_attribute'];
            }
        }
        return $attrs;
    }

    private function getSizes()
    {

        $sql = 'SELECT `id_size`, `bust`, `waist`, `hips`, `length`, `active` FROM `' . _DB_PREFIX_ . 'sin_clothes_sizing` WHERE id_product = ' . (int) Tools::getValue('id_product');
        $result = Db::getInstance()->executeS($sql);

        if ((!empty($result)) && ($result)) {
            $arraySizes = array_flip($this->arraySizes);
            foreach ($result as $key => $value) {
                $name = $arraySizes[$result[$key]['id_size']];
                $result[$name] = $result[$key];
                unset($result[$key]);
            }
            return $result;
        } else {
            return false;
        }
    }

    //wsydliwe przepisanie
    private $rewrite = [
        1 => 23,
        2 => 1,
        3 => 2,
        4 => 3,
        5 => 4,
        6 => 29,
    ];


    private function contents()
    {
        $array['display_name'] = $this->displayName;
        $array['section_title'] = $this->l('Size table');
        $array['table_title'] = $this->l('Sizing for model:');
        $array['table_bust'] = $this->l('bust (cm)');
        $array['table_waist'] = $this->l('waist (cm)');
        $array['table_hips'] = $this->l('hips (cm)');
        $array['table_length'] = $this->l('length (cm)');
        $array['show_xs'] = $this->l('Show XS');
        $array['show_xxl'] = $this->l('Show XXL');
        return $array;
    }

    private function isVisible()
    {

        foreach ($this->arraySizes as $key => $value) {
            if (isset($this->realSizes[$this->rewrite[$value]])) {
                $array['names'][$key] = ($this->realSizes[$this->rewrite[$value]] !== null) ? true : false;
            } else {
                $array['names'][$key] = false;
            }
        }
        foreach ($this->dimentionsNames as $name) {
            ((($this->getSizes['s'][$name]) == null)      ||
                (($this->getSizes['s'][$name]) == 0)      ||
                (($this->getSizes['xl'][$name]) == null)  ||
                (($this->getSizes['xl'][$name]) == 0))    ?
                $array[$name] = false : $array[$name] = true;
        }
        return $array;
    }

    private function request($string)
    {
        (isset($_REQUEST[$string])) ? $request = $_REQUEST[$string] : $request = null;
        if ($request == null) {
            return null;
        } elseif (preg_match('/[0-9]{1,3}/', $request)) {
            return $request;
        } else {
            null;
        }
    }

    private function countSize()
    {
        foreach ($this->baseSizesNames as $baseSizeName) {
            if (($this->request($baseSizeName)) == null) {
                $sizes[$baseSizeName] = null;
            } else {
                $sizes[$baseSizeName] = $this->request($baseSizeName);
            }
        }
        $strings = $this->dimentionsNames;
        foreach ($strings as $string) {
            if (($sizes[$string . '_xl'] == 0) || (($sizes[$string . '_s'])) == 0) {
                $average[$string] = 0;
            } else {
                $average[$string] = (($sizes[$string . '_xl']) - ($sizes[$string . '_s'])) / 3;
            }
        }
        foreach ($strings as $string) {
            ($sizes[$string . '_s'] == null) ? $array[$string . '_xs'] = null : $array[$string . '_xs'] = $sizes[$string . '_s'] - ceil($average[$string]);
            ($sizes[$string . '_s'] == null) ? $array[$string . '_s'] = null : $array[$string . '_s'] = $sizes[$string . '_s'];
            ($sizes[$string . '_s'] == null) ? $array[$string . '_m'] = null : $array[$string . '_m'] = $sizes[$string . '_s'] + floor($average[$string]);
            ($sizes[$string . '_xl'] == null) ? $array[$string . '_l'] = null : $array[$string . '_l'] = $sizes[$string . '_xl'] - ceil($average[$string]);
            ($sizes[$string . '_xl'] == null) ? $array[$string . '_xl'] = null : $array[$string . '_xl'] = $sizes[$string . '_xl'];
            ($sizes[$string . '_xl'] == null) ? $array[$string . '_xxl'] = null : $array[$string . '_xxl'] = $sizes[$string . '_xl'] + ceil($average[$string]);
        }
        return $array;
    }

    private function extremeSizes($value)
    {
        (isset($_REQUEST['show_xs'])) ? $xs = $_REQUEST['show_xs'] : $xs = null;
        (isset($_REQUEST['show_xxl'])) ? $xxl = $_REQUEST['show_xxl'] : $xxl = null;
        if ($value == 1) {
            $result = $xs;
        } elseif ($value == 6) {
            $result = $xxl;
        } else {
            $result = null;
        }
        return $result;
    }

    private function createSizeObjects()
    {
        $sizesId = $this->arraySizes;
        $sizesValues = $this->countSize();
        foreach ($sizesId as $key => $value) {
            $object = new SizeObject();
            $object->id_product = Tools::getValue('id_product');
            $object->id_size = $value;
            foreach ($this->dimentionsNames as $name) {
                $object->$name = $sizesValues[$name . '_' . $key];
            }
            $object->active = $this->extremeSizes($value);
            $array[] = $object;
        }
        return $array;
    }

    private function showTable()
    {
        $arraySizes = array_flip($this->arraySizes);
        foreach ($this->dimentionsNames as $name) {
            foreach ($arraySizes as $sizeName) {
                $$sizeName = $this->getSizes[$sizeName][$name];
                $isAllNull[$name . '_' . $sizeName] = $$sizeName;
            }
            ((($s == null) && ($xl != null)) || (($s != null) && ($xl == null))) ? $noValue[$name] = true : $noValue[$name] = false;
        }
        foreach ($noValue as $key => $value) {
            if ($value == true) {
                $foundNullValue = true;
                break;
            } elseif ($value == false) {
                $foundNullValue = false;
            }
        }
        foreach ($isAllNull as $value) {
            if ($value != null) {
                $isNull = false;
                break;
            } else {
                $isNull = true;
            }
        }
        return (($foundNullValue) || ($isNull)) ? false : true;
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path . 'css/sinclothessizing.css', 'all');
    }

    public function hookBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path . 'css/sinclothessizing.css', 'all');
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        if (!in_array(Tools::getValue('id_product'), array(false, 0, '0'), true)) {
            $this->context->smarty->assign(array(
                'sizes' => $this->getSizes,
                'visible' => $this->isVisible(),
                'contents' => $this->contents(),
                'show' => $this->showTable(),
            ));
            return $this->display(__FILE__, 'sinclothessizing_admin.tpl');
        } else {
            $this->context->controller->warnings[] = $this->l(
                'You must save this product before configuring sizes.'
            );
        }
    }

    public function hookActionProductUpdate($params)
    {
        $objects = SizeObject::getSizeObjects($this->id_product);
        if (!$objects) {
            foreach ($this->countSize() as $value) {
                if ($value != null) {
                    $result = true;
                    break;
                } else {
                    $result = false;
                }
            }
            if ($result) {
                $objects = $this->createSizeObjects();
                foreach ($objects as $object) {
                    $object->save();
                }
            } else {
                return false;
            }
        } else {
            foreach ($objects as $object) {
                $arraySizes = array_flip($this->arraySizes);
                foreach ($this->dimentionsNames as $name) {
                    if (($this->countSize()[$name . '_' . $arraySizes[$object->id_size]]) == null) {
                        $object->$name = $object->$name;
                    } else {
                        $object->$name = $this->countSize()[$name . '_' . $arraySizes[$object->id_size]];
                    }
                }
                $object->active = $this->extremeSizes($object->id_size);
                $object->save();
            }
        }
    }

    public function hookActionObjectProductDeleteAfter($params)
    {
        SizeObject::deleteProductData($this->id_product);
    }




    public function hookDisplayRightColumnProduct($params)
    {

        $product = new Product($this->id_product);
        $this->context->smarty->assign(
            array(
                'product_reference' => $product->reference,
                'sizes' => $this->getSizes,
                'visible' => $this->isVisible(),
                'contents' => $this->contents(),
                'show' => $this->showTable(),
            )
        );
        return $this->display(__FILE__, 'sinclothessizing.tpl');
    }
}
