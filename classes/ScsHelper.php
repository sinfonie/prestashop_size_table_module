<?php

/**
 * ScsHelper
 * @author Maciej Rumiński <ruminski.maciej@gmail.com>
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

class ScsHelper
{
  /**
   * Method returns re-mapped attribute group where keys are  `id_attribute` and values are `name` 
   * @param array $lang
   * @param array $id_group
   * @return array
   */
  public static function getAttributes($id_group, $lang = 'en'): array
  {
    $attributes = array_map('self::mapAttributes', AttributeGroup::getAttributes($lang, $id_group));
    $output = [];
    foreach ($attributes as $val) {
      $output[key($val)] = $val[key($val)];
    }
    return $output;
  }

  /**
   * Method returns non color groups
   * @param array $lang
   * @return array
   */
  public static function getGroupsAttributes($lang = 'en'): array
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
   * Remap method
   */
  private static function mapAttributes($att)
  {
    return  [$att['id_attribute'] => $att['name']];
  }

  /**
   * Method an array lang properties from model
   * @return array
   */
  public static function getLangProperties(): array
  {
    $langs = Language::getLanguages(true);
    $no_properties = Tools::getValue('no_properties');
    $arr = [];
    for ($i = 1; $i <= $no_properties; $i++) {
      foreach ($langs as $lang) {
        $arr[$i][$lang['id_lang']] = Tools::getValue('property_' . $i . '_' . $lang['id_lang']);
      }
    }
    return $arr;
  }
}
