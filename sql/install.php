<?php

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'sin_clothes_sizing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
	`id_product` int(11) NOT NULL,
	`id_size` int(11) NOT NULL,
	`bust` int(3),
	`waist` int(3),
	`hips` int(3),
	`length` int(3),
	`active` tinyint(1),
	`date_add` datetime DEFAULT CURRENT_TIMESTAMP,
	`date_upd` datetime NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY  (`id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'scs_configurations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
	`attr_group_id` int(11) NOT NULL,
	`dim_start` int(11) NOT NULL,
	`dim_end` int(11) NOT NULL,
	`name` varchar(30000) NOT NULL,
	`properties` varchar(30000) NOT NULL,
	`active` boolean NOT NULL,
	`date_add` datetime DEFAULT CURRENT_TIMESTAMP,
	`date_upd` datetime NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY  (`id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


foreach ($sql as $query) {
	if (Db::getInstance()->execute($query) == false) {
		return false;
	}
}
