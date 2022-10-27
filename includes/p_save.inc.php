<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2016-2022
*/

//controleer of mag opslaan
if (permissioncheck('bewerk') !== true) {
	echo 'niet toegestaan';
	exit;
}

if (($_GET['s'] == 'p') && !empty($_POST)) {
	
	//decide edit or add
	if (!empty($_POST['id'])) {
		$qry = "SELECT * 
		FROM `".$sql['database']."`.`".$sql['table_p']."`
		WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_POST['id'])."'
		LIMIT 1";
		$res = mysqli_query($sql['link'], $qry);
		if (mysqli_num_rows($res)) {
			$edit = TRUE;
		}
	}
	//check if no date is set and then set dates to 1970-01-01
	if ($_POST['nodate'] == 'true') {
		$_POST['date_start'] = '1970-01-01';
		$_POST['date_end'] = '1970-01-01';
	}
	
	//edit
	if ($edit === TRUE) {
		$qry = "UPDATE `".$sql['database']."`.`".$sql['table_p']."`
		SET
		`datetime_start` = '".date('Y-m-d H:i:s', strtotime($_POST['date_start'].' '.$_POST['time_start']))."',
		`datetime_end` = '".date('Y-m-d H:i:s', strtotime($_POST['date_end'].' '.$_POST['time_end']))."',
		`road` = '".mysqli_real_escape_string($sql['link'], $_POST['road'])."',
		`location` = '".mysqli_real_escape_string($sql['link'], $_POST['location'])."',
		`name` = '".mysqli_real_escape_string($sql['link'], $_POST['name'])."',
		`description` = '".mysqli_real_escape_string($sql['link'], $_POST['description'])."',
		`spare` = '".mysqli_real_escape_string($sql['link'], $_POST['spare'])."',
		`scenario` = '".mysqli_real_escape_string($sql['link'], $_POST['scenario'])."',
		`scenario_naam` = '".mysqli_real_escape_string($sql['link'], $_POST['scenario_naam'])."',
		`type` = '".mysqli_real_escape_string($sql['link'], $_POST['type'])."',
		`user_id_assigned` = '".mysqli_real_escape_string($sql['link'], $_POST['user_id_assigned'])."',
		`user_id_edit` = '".getuser()."'
		WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_POST['id'])."'";
		if (mysqli_query($sql['link'], $qry)) $msg = 's001';
		else $msg = 'e001';
		echo mysqli_error($sql['link']);
		$id = (int) $_POST['id'];
	}
	//add
	else {
		$qry = "INSERT INTO `".$sql['database']."`.`".$sql['table_p']."`
		SET
		`datetime_start` = '".date('Y-m-d H:i:s', strtotime($_POST['date_start'].' '.$_POST['time_start']))."',
		`datetime_end` = '".date('Y-m-d H:i:s', strtotime($_POST['date_end'].' '.$_POST['time_end']))."',
		`road` = '".mysqli_real_escape_string($sql['link'], $_POST['road'])."',
		`location` = '".mysqli_real_escape_string($sql['link'], $_POST['location'])."',
		`name` = '".mysqli_real_escape_string($sql['link'], $_POST['name'])."',
		`description` = '".mysqli_real_escape_string($sql['link'], $_POST['description'])."',
		`spare` = '".mysqli_real_escape_string($sql['link'], $_POST['spare'])."',
		`scenario` = '".mysqli_real_escape_string($sql['link'], $_POST['scenario'])."',
		`scenario_naam` = '".mysqli_real_escape_string($sql['link'], $_POST['scenario_naam'])."',
		`type` = '".mysqli_real_escape_string($sql['link'], $_POST['type'])."',
		`user_id_assigned` = '".mysqli_real_escape_string($sql['link'], $_POST['user_id_assigned'])."',
		`user_id_create` = '".getuser()."',
		`user_id_edit` = '".getuser()."'";
		if (mysqli_query($sql['link'], $qry)) $msg = 's001';
		else $msg = 'e001';
		echo mysqli_error($sql['link']);
		$id = mysqli_insert_id($sql['link']);
	}
	
	if (isset($_POST['saveandcopy'])) {
		header('Location: ?p=p&copyfrom='.$id);
	}
	if (isset($_POST['saveandview'])) {
		header('Location: ?p=p_view&id='.$id);
	}
	else {
		//fix browser back button
		header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php?msg=' . $msg, TRUE, 303);
	}
}
?>