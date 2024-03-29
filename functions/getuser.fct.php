<?php
/*
	ovmlog - logtool voor operationeel verkeersmanagement
	Copyright (C) 2013, 2022 Gemeente Den Haag, Netherlands
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

//function to get user id or name
//var getuser ( [ str $type ] )
//returns:
//bool false if no user logged in
//int $user_id if user is logged in, or
//str $user_name if $type == 'name'

if (!function_exists('getuser')) { function getuser($type='id') {
	include('logincheck.inc.php');
	if ($type == 'name') {
		return logincheck('username');
	}
	else {
		return logincheck('id');
	}
}}
?>