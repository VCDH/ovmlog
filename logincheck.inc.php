<?php
/*
 	scenariobrowser - viewer en editor voor verkeersmanagementscenario's
    Copyright (C) 2016-2019 Gemeente Den Haag, Netherlands
	ovmlog - logtool voor operationeel verkeersmanagement
	Copyright (C) 2022 Gemeente Den Haag, Netherlands
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
	//haal gegevens uit cookie
	$cookie = unserialize($_COOKIE['login']);
	$user_id = $cookie['id'];
	$token = $cookie['token'];
	//controleer of waarden in het cookie zinnig zijn
	if (is_numeric($user_id) && (strlen($token) == 64)) {
		//include database gegevens
		include('dbconnect.inc.php');
		//stel karakterset in voor mysqli_real_escape_string
		mysqli_set_charset($db['link'], 'latin1');
		//query om tabel lezen
		$sql = "SELECT
		`id`, `password`, `token`, `email`, `username`, `accesslevel`, `organisation`
		FROM `".$db['prefix']."users`
		WHERE `id` = '" . mysqli_real_escape_string($db['link'], $user_id) . "'
		AND `disabled` = 0
		LIMIT 1";
		//voer query uit
		$result = mysqli_query($db['link'], $sql);
		if (mysqli_num_rows($result) == 1) {
			$data = mysqli_fetch_assoc($result);
			if ($token == $data['password']) {
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
		}
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