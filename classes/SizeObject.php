<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class SizeObject extends ObjectModel
{
    public $id;
    public $id_product;
    public $id_size;
    public $bust;
    public $waist;
    public $hips;
    public $length;
    public $active;

    public static $definition = array(
        'table' => 'sin_clothes_sizing',
        'primary' => 'id',
        'multilang' => false,
        'fields' => array(
            'id_product' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ),
            'id_size' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ),
            'bust' => array(
                'type' => self::TYPE_NOTHING,
                'validate' => 'isUnsignedInt',
                'allow_null' => true,
            ),
            'waist' => array(
                'type' => self::TYPE_NOTHING,
                'validate' => 'isUnsignedInt',
                'allow_null' => true,
            ),
            'hips' => array(
                'type' => self::TYPE_NOTHING,
                'validate' => 'isUnsignedInt',
                'allow_null' => true,
            ),
            'length' => array(
                'type' => self::TYPE_NOTHING,
                'validate' => 'isUnsignedInt',
                'allow_null' => true,
            ),
            'active' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'allow_null' => true,
            ),
        ),
    );

    public static function getSizeObjects($id_product)
    {
        $sql = 'SELECT `id`, `id_size`, `bust`, `waist`, `hips`, `length`, `active` FROM `' . _DB_PREFIX_ . 'sin_clothes_sizing` ' .
            'WHERE `id_product` = ' . $id_product;
        $request = Db::getInstance()->executeS($sql);
        $array = [];
        if ($request === false) {
            return false;
        } else {
            foreach ($request as $value) {
                $object = new SizeObject();
                $object->id = $value['id'];
                $object->id_product = $id_product;
                $object->id_size = $value['id_size'];
                $object->bust = $value['bust'];
                $object->waist = $value['waist'];
                $object->hips = $value['hips'];
                $object->length = $value['length'];
                $object->active = $value['active'];
                $array[] = $object;
            }
            return $array;
        }
    }

    public static function deleteProductData($id_product)
    {
        foreach (self::getSizeObjects($id_product) as $object) {
            if (is_object($object)) {
                $object->delete();
            }
        }
    }
}
