<?php

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'scs_models` (
  `id_model`  int(10) unsigned NOT NULL auto_increment,
	`attr_group_id` int(11) NOT NULL,
	`dim_start` int(11) NOT NULL,
	`dim_end` int(11) NOT NULL,
	`name` varchar(30000) NOT NULL,
	`active` boolean NOT NULL,
	`date_add` datetime DEFAULT CURRENT_TIMESTAMP,
	`date_upd` datetime NULL ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY  (`id_model`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'scs_models_lang`(
	`id_model` int(10) unsigned NOT NULL,
	`id_lang` int(10) unsigned NOT NULL,
	`properties` varchar(30000) NOT NULL,
	`date_add` datetime DEFAULT CURRENT_TIMESTAMP,
	`date_upd` datetime NULL ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id_model`, `id_lang`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'scs_products`(
	`id_product_model` int(10) unsigned NOT NULL auto_increment,
	`id_product` int(10) unsigned NOT NULL,
	`id_model` int(10) unsigned NOT NULL,
	`active` boolean NOT NULL,
	`date_add` datetime DEFAULT CURRENT_TIMESTAMP,
	`date_upd` datetime NULL ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id_product_model`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'scs_products_dimensions`(
	`id_dimension` int(10) unsigned NOT NULL auto_increment,
	`id_product_model` int(10) unsigned NOT NULL,
	`id_property` int(10) unsigned NOT NULL,
	`dim_start` int(11),
	`dim_end` int(11),
	`active` boolean NOT NULL,
	`date_add` datetime DEFAULT CURRENT_TIMESTAMP,
	`date_upd` datetime NULL ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id_dimension`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';


foreach ($sql as $query) {
	if (Db::getInstance()->execute($query) == false) {
		return false;
	}
}
