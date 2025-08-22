<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2021, 2022, 2025
*/

//database credentials and settings
$sql['server'] = 'localhost';
$sql['database'] = 'ovmlog';
$sql['table_prefix'] = 'ovm_';
$db['prefix'] = $sql['table_prefix'];
$sql['table_i'] = $sql['table_prefix'].'incidenten';
$sql['table_id'] = $sql['table_prefix'].'incidenten_details';
$sql['table_w'] = $sql['table_prefix'].'werkzaamheden';
$sql['table_e'] = $sql['table_prefix'].'evenementen';
$sql['table_p'] = $sql['table_prefix'].'gepland';
$sql['table_d'] = $sql['table_prefix'].'daglog';
$sql['table_users'] = $sql['table_prefix'].'user';
$sql['table_bijlagen'] = $sql['table_prefix'].'bijlagen';
$sql['user'] = 'root';
$sql['password'] = '';
?>