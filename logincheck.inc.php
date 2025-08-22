<?php
/*
 	scenariobrowser - viewer en editor voor verkeersmanagementscenario's
    Copyright (C) 2016-2019 Gemeente Den Haag, Netherlands
	ovmlog - logtool voor operationeel verkeersmanagement
	Copyright (C) 2022, 2025 Gemeente Den Haag, Netherlands
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
require('auth.cfg.php');
if (!function_exists('logincheck')) { function logincheck($type='bool') {
	require('dbconnect.inc.php');
	require('config.inc.php');
    //check if the user is logged in
    //retrieve cookie
    $cookievalue = unserialize($_COOKIE[$cfg['cookie']['name']]);
    if (!is_numeric($cookievalue[0])) {
        return FALSE;
    }
    //TODO: put this in a session variable such that it doesn't have to be retrieved from the database every time
    
    //match user info with db
    $qry = "SELECT `".$db['prefix']."user`.`id` AS `id`, `".$db['prefix']."user`.`organisation` AS `organisation`, `".$db['prefix']."user`.`email` AS `email`, `".$db['prefix']."user`.`username` AS `username`, `".$db['prefix']."user`.`accesslevel` AS `accesslevel`, `".$db['prefix']."user_login_tokens`.`token` AS `token`, `".$db['prefix']."user`.`organisation` AS `organisation` FROM `".$db['prefix']."user_login_tokens`
    LEFT JOIN `".$db['prefix']."user`
    ON `".$db['prefix']."user_login_tokens`.`user_id` = `".$db['prefix']."user`.`id`
    WHERE `".$db['prefix']."user_login_tokens`.`user_id` = '" . mysqli_real_escape_string($db['link'], $cookievalue[0]) . "'
    AND `".$db['prefix']."user_login_tokens`.`token` = '" . mysqli_real_escape_string($db['link'], $cookievalue[1]) . "'";
    $res = mysqli_query($db['link'], $qry);
    if ($data = mysqli_fetch_assoc($res)) {
		$_SESSION['id'] = $data['id'];
		$_SESSION['org'] = $data['organisation'];
		$_SESSION['email'] = $data['email'];
		$_SESSION['name'] = $data['username'];
		$_SESSION['accesslevel'] = $data['accesslevel'];
		if ($type == 'id') {
			return $data['id'];
		}
		elseif ($type == 'username') {
			return $data['username'];
		}
		return TRUE;
    }
    return FALSE;
}}

if (!function_exists('permissioncheck')) { function permissioncheck($permission, $check_org = FALSE) {
	global $auth;
	if (($check_org !== FALSE) && ($check_org != $_SESSION['org'])) {
		return FALSE;
	}
	if (array_key_exists($permission, $auth)) {
		if ($_SESSION['accesslevel'] >= $auth[$permission]) {
			return TRUE;
		}
	}
	return FALSE;
}}
?> 