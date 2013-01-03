<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

if (($_GET['s'] == 'e') && !empty($_POST)) {
	
	//decide edit or add
	if (!empty($_POST['id'])) {
		$qry = "SELECT * 
		FROM `".$sql['database']."`.`".$sql['table_e']."`
		WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_POST['id'])."'
		LIMIT 1";
		$res = mysqli_query($sql['link'], $qry);
		if (mysqli_num_rows($res)) {
			$edit = TRUE;
		}
	}
	
	//edit
	if ($edit === TRUE) {
		
		$qry = "UPDATE `".$sql['database']."`.`".$sql['table_e']."`
		SET
		`datetime_start` = '".date('Y-m-d H:i:s', strtotime($_POST['date_start'].' '.$_POST['time_start']))."',
		`datetime_end` = '".date('Y-m-d H:i:s', strtotime($_POST['date_end'].' '.$_POST['time_end']))."',
		`name` = '".mysqli_real_escape_string($sql['link'], $_POST['name'])."',
		`description` = '".mysqli_real_escape_string($sql['link'], $_POST['description'])."',
		`scenario` = '".mysqli_real_escape_string($sql['link'], $_POST['scenario'])."'
		WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_POST['id'])."'";
		if (mysqli_query($sql['link'], $qry)) $msg = 's001';
		else $msg = 'e001';
		echo mysqli_error($sql['link']);
	}
	//add
	else {
		$qry = "INSERT INTO `".$sql['database']."`.`".$sql['table_e']."`
		SET
		`datetime_start` = '".date('Y-m-d H:i:s', strtotime($_POST['date_start'].' '.$_POST['time_start']))."',
		`datetime_end` = '".date('Y-m-d H:i:s', strtotime($_POST['date_end'].' '.$_POST['time_end']))."',
		`name` = '".mysqli_real_escape_string($sql['link'], $_POST['name'])."',
		`description` = '".mysqli_real_escape_string($sql['link'], $_POST['description'])."',
		`scenario` = '".mysqli_real_escape_string($sql['link'], $_POST['scenario'])."'";
		if (mysqli_query($sql['link'], $qry)) $msg = 's001';
		else $msg = 'e001';
		echo mysqli_error($sql['link']);
	}
}
?>