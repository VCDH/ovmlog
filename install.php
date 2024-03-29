<?php
/*
	ovmlog - logtool voor operationeel verkeersmanagement
	Copyright (C) 2013-2017, 2021-2022 Gemeente Den Haag, Netherlands
    Developed by Jasper Vries
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/

include('db.inc.php');

//connect to database
$sql['link'] = mysqli_connect($sql['server'], $sql['user'], $sql['password']);

//create database
$qry = "CREATE DATABASE IF NOT EXISTS `".$sql['database']."`
		CHARACTER SET = latin1
		COLLATE = latin1_general_ci";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);

//create table "incidenten"
$qry = "CREATE TABLE IF NOT EXISTS `".$sql['database']."`.`".$sql['table_i']."` (
		`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`date` DATE NOT NULL,
		`road` TINYTEXT,
		`location` TINYTEXT,
		`scenario` TINYTEXT,
		`open` BOOLEAN DEFAULT 1,
        `review` BOOLEAN DEFAULT 0,
		`user_id_create` INT UNSIGNED DEFAULT 0,
		`user_id_edit` INT UNSIGNED DEFAULT 0
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);
/*
$qry = "ALTER TABLE `".$sql['database']."`.`".$sql['table_i']."`
		ADD `regelaanpak` BOOLEAN DEFAULT 0
		AFTER `review`";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);
*/
//create table "incidenten_details"
$qry = "CREATE TABLE IF NOT EXISTS `".$sql['database']."`.`".$sql['table_id']."` (
		`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`parent_id` INT UNSIGNED NOT NULL,
		`time` TIME NOT NULL,
		`description` TEXT,
		`contact` TINYTEXT,
		`user_id_create` INT UNSIGNED DEFAULT 0,
		`user_id_edit` INT UNSIGNED DEFAULT 0
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);

//create table "gepland"
$qry = "CREATE TABLE IF NOT EXISTS `".$sql['database']."`.`".$sql['table_p']."` (
		`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `type` ENUM( 'w', 'e' ) NOT NULL,
        `name` VARCHAR( 255 ) NULL,
		`datetime_start` DATETIME NOT NULL,
		`datetime_end` DATETIME NOT NULL,
		`road` TINYTEXT,
		`location` TINYTEXT,
		`description` TEXT,
		`spare` BOOLEAN NOT NULL DEFAULT 0,
		`scenario` TINYTEXT,
		`user_id_assigned` INT UNSIGNED DEFAULT 0,
		`user_id_create` INT UNSIGNED DEFAULT 0,
		`user_id_edit` INT UNSIGNED DEFAULT 0
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);
/*
$qry = "ALTER TABLE `".$sql['database']."`.`".$sql['table_p']."`
		ADD `scenario_naam` TINYTEXT
		AFTER `scenario`";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);
*/
//create table "daglog"
$qry = "CREATE TABLE IF NOT EXISTS `".$sql['database']."`.`".$sql['table_d']."` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`datetime` DATETIME NOT NULL,
	`description` TEXT NOT NULL,
	`user_id_create` INT UNSIGNED DEFAULT 0,
	`user_id_edit` INT UNSIGNED DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);
/*
$qry = "ALTER TABLE `".$sql['database']."`.`".$sql['table_d']."`
		ADD `sticky` BOOLEAN DEFAULT 0
		AFTER `description`";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);

$qry = "ALTER TABLE `".$sql['database']."`.`".$sql['table_d']."`
		ADD `review` BOOLEAN DEFAULT 0
		AFTER `sticky`";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);
*/
//create table "users"
$qry = "CREATE TABLE IF NOT EXISTS `".$sql['database']."`.`".$sql['table_users']."` (
		`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`username` VARCHAR(64),
		`password` VARCHAR(32),
		`accesscode` TEXT,
		`email` VARCHAR(255),
        `disabled` BOOLEAN DEFAULT FALSE
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);

$qry = "ALTER TABLE `".$sql['database']."`.`".$sql['table_users']."`
		ADD `token` MEDIUMTEXT AFTER `email`,
		ADD `phone` TINYTEXT NULL DEFAULT NULL AFTER `email`,
		ADD `organisation` INT UNSIGNED NOT NULL AFTER `email`,
		ADD `accesslevel` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `email`,
		DROP `accesscode`,
		CHANGE `password` `password` VARCHAR(64)";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);

$qry = "CREATE TABLE IF NOT EXISTS `".$sql['database']."`.`".$sql['table_bijlagen']."` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`parent_id` INT UNSIGNED NOT NULL,
	`datum` DATETIME NOT NULL,
	`user_id` INT UNSIGNED NOT NULL,
	`bestandsnaam` TINYTEXT NOT NULL,
	`grootte` INT NOT NULL,
	`bestand` VARCHAR(40) NOT NULL,
	PRIMARY KEY (`id`)
	)
	CHARACTER SET 'latin1' 
	COLLATE 'latin1_general_ci' 
	ENGINE=MyISAM";
	mysqli_query($sql['link'], $qry);
	echo mysqli_error($sql['link']);

//create store
if (!is_dir('attachments')) {
	mkdir('attachments');
	$subdirs = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
	foreach ($subdirs as $subdir) {
		mkdir('attachments/'.$subdir);
	}
	echo 'created attachments directories'.PHP_EOL;
}
file_put_contents('attachments/.htaccess', 'deny from all');

echo '<p>done</p>';
?>