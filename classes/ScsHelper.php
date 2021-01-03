<?php

if (!defined('_PS_VERSION_')) {
  exit;
}

class ScsHelper
{

  public static function getAttributes($lang, $id_group): array
  {
    $attributes = array_map('self::mapAttributes', AttributeGroup::getAttributes($lang, $id_group));
    $output = [];
    foreach ($attributes as $val) {
      $output[key($val)] = $val[key($val)];
    }
    return $output;
  }

  public static function getGroupsAttributes($lang = 'en'): array
  {
    $attributeGroups = AttributeGroup::getAttributesGroups($lang);
    $filteredGroups = array_filter($attributeGroups, function ($att) {
      if ($att['is_color_group'] === '0') return true;
    });
    return $filteredGroups;
  }


  private static function mapAttributes($att)
  {
    return  [$att['id_attribute'] => $att['name']];
  }
}
