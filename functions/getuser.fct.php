<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

//function to get user id or name
//var getuser ( [ str $type ] )
//returns:
//bool false if no user logged in
//int $user_id if user is logged in, or
//str $user_name if $type == 'user'

if (!function_exists('getuser')) { function getuser($type='id') {
	include('db.inc.php');
	include('cookie.inc.php');
	
	//check if cookie
	$cookievalue = unserialize($_COOKIE[$cookie['name']]);
	if (!is_numeric($cookievalue[0])) return FALSE;
	else {
		//connect to database
		$sql['link'] = mysqli_connect($sql['server'], $sql['user'], $sql['password']);
		if ($type == 'name') $type = 'username';
		else $type = 'id';
		//select from database
		$qry = "SELECT `".$type."` 
			FROM `".$sql['database']."`.`".$sql['table_users']."`
			WHERE `id` = '".mysqli_real_escape_string($sql['link'], $cookievalue[0])."'";
		$res = mysqli_query($sql['link'], $qry);
		if (mysqli_num_rows($res) == 1) {
			$data = mysqli_fetch_row($res);
			return $data[0];
		}
		else return FALSE;
	}
}}
?>