<?php

if (!defined('_PS_VERSION_')) {
  exit;
}

class SinClothesSizesHelper
{

  public static function getAttributes($lang, $id_group)
  {
    $attributes = AttributeGroup::getAttributes($lang, $id_group);
    return array_map('self::mapAttributes', $attributes);
  }

  public static function getGroupsAttributes($lang)
  {
    $attributeGroups = AttributeGroup::getAttributesGroups($lang);
    $filteredGroups = array_filter($attributeGroups, 'self::filterGroupsAttributes');
    return array_map('self::mapGroupsAttributes', $filteredGroups);
  }

  private static function filterGroupsAttributes($att)
  {
    if ($att['is_color_group'] === '0') {
      return true;
    }
  }

  private static function mapGroupsAttributes($att)
  {
    return [
      'name' => $att['name'],
      'id' => $att['id_attribute_group'],
    ];
  }

  private static function mapAttributes($att)
  {
    return $att['name'];
  }
}
