<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

if (($_GET['s'] == 'i') && !empty($_POST)) {
	
	$date = date('Y-m-d', strtotime($_POST['date']));
	
	$qry = "INSERT INTO `".$sql['database']."`.`".$sql['table_i']."`
	SET
	`date` = '".$date."',
	`road` = '".mysqli_real_escape_string($sql['link'], $_POST['road'])."',
	`location` = '".mysqli_real_escape_string($sql['link'], $_POST['location'])."',
	`scenario` = '".mysqli_real_escape_string($sql['link'], $_POST['scenario'])."'";
	if (mysqli_query($sql['link'], $qry)) {
		
		$id = mysqli_insert_id($sql['link']);
		$time = date('H:i:s', strtotime($_POST['time']));
		
		$qry = "INSERT INTO `".$sql['database']."`.`".$sql['table_id']."`
		SET
		`parent_id` = ".$id.",
		`time` = '".$time."',
		`description` = '".mysqli_real_escape_string($sql['link'], nl2br($_POST['description']))."',
		`contact` = '".mysqli_real_escape_string($sql['link'], $_POST['contact'])."'";
		if (mysqli_query($sql['link'], $qry)) {
			$msg = 's001';
		}
		else $msg = 'e002';
	}
	else $msg = 'e001';
	echo mysqli_error($sql['link']);
}

?>