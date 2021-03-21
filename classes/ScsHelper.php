<?php

/**
 * ScsHelper
 * @author <sinfonie@o2.pl>
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

class ScsHelper
{
  /**
   * Method returns re-mapped attribute group where keys are  `id_attribute` and values are `name` 
   * @param int $lang
   * @param int $id_group
   * @return array
   */
  public static function getAttributes(int $id_group, int $lang): array
  {
    $attributes = AttributeGroup::getAttributes($lang, $id_group);
    if ($attributes && !empty($attributes)) return array_column($attributes, 'name', 'id_attribute');
    return array();
  }

  /**
   * Method returns non color groups
   * @param int $lang
   * @return array
   */
  public static function getGroupsAttributes(int $lang): array
  {
    $attributeGroups = AttributeGroup::getAttributesGroups($lang);
    $filteredGroups = array_filter($attributeGroups, function ($att) {
      if ($att['is_color_group'] === '0') return true;
    });
    $groups = [];
    if (!empty($filteredGroups)) {
      $groups = array_column($filteredGroups, null, 'id_attribute_group');
    }
    return $groups;
  }

  /**
   * Method returns an array of lang properties from model
   * @return array
   */
  public static function getLangProperties($serialized = false): array
  {
    $langs = Language::getLanguages(true);
    $no_properties = Tools::getValue('no_properties');
    $arr = [];
    for ($i = 1; $i <= $no_properties; $i++) {
      foreach ($langs as $lang) {
        $arr[$lang['id_lang']][$i] = Tools::getValue('property_' . $i . '_' . $lang['id_lang']);
      }
    }
    if ($serialized) {
      array_walk($arr, function (&$val) {
        $val = serialize($val);
      });
    }
    return $arr;
  }
}
