<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
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
		`user_id_create` INT UNSIGNED DEFAULT 0,
		`user_id_edit` INT UNSIGNED DEFAULT 0
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);

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

//create table "werkzaamheden"
$qry = "CREATE TABLE IF NOT EXISTS `".$sql['database']."`.`".$sql['table_w']."` (
		`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`datetime_start` DATETIME NOT NULL,
		`datetime_end` DATETIME NOT NULL,
		`road` TINYTEXT,
		`location` TINYTEXT,
		`description` TEXT,
		`scenario` TINYTEXT,
		`user_id_create` INT UNSIGNED DEFAULT 0,
		`user_id_edit` INT UNSIGNED DEFAULT 0
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);

//create table "evenementen"
$qry = "CREATE TABLE IF NOT EXISTS `".$sql['database']."`.`".$sql['table_e']."` (
		`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`datetime_start` DATETIME NOT NULL,
		`datetime_end` DATETIME NOT NULL,
		`name` TINYTEXT,
		`description` TEXT,
		`scenario` TINYTEXT,
		`user_id_create` INT UNSIGNED DEFAULT 0,
		`user_id_edit` INT UNSIGNED DEFAULT 0
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);

//create table "evenementen"
$qry = "CREATE TABLE IF NOT EXISTS `".$sql['database']."`.`".$sql['table_users']."` (
		`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`username` VARCHAR(64),
		`password` VARCHAR(32),
		`accesscode` TEXT,
		`email` VARCHAR(255)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);

echo '<p>done</p>';
?>