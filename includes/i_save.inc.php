<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

if (($_GET['s'] == 'i') && !empty($_POST)) {
	
	//decide edit or add
	if (!empty($_POST['id'])) {
		$qry = "SELECT * 
		FROM `".$sql['database']."`.`".$sql['table_i']."`
		WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_POST['id'])."'
		LIMIT 1";
		$res = mysqli_query($sql['link'], $qry);
		if (mysqli_num_rows($res)) {
			$edit = TRUE;
		}
	}
	
	//edit
	if ($edit === TRUE) {
		
		$qry = "UPDATE `".$sql['database']."`.`".$sql['table_i']."`
		SET
		`date` = '".date('Y-m-d', strtotime($_POST['date']))."',
		`road` = '".mysqli_real_escape_string($sql['link'], $_POST['road'])."',
		`location` = '".mysqli_real_escape_string($sql['link'], $_POST['location'])."',
		`scenario` = '".mysqli_real_escape_string($sql['link'], $_POST['scenario'])."'";
		if ($_POST['closed'] == 'true') $qry .= ", `open` = 0 ";
		$qry .= "WHERE `id` = '".$_POST['id']."'";
		if (mysqli_query($sql['link'], $qry)) {
			
			foreach ($_POST['time'] as $id => $time) {
				$qry = "UPDATE `".$sql['database']."`.`".$sql['table_id']."`
				SET
				`time` = '".date('H:i:s', strtotime($time))."',
				`description` = '".mysqli_real_escape_string($sql['link'], $_POST['description'][$id])."',
				`contact` = '".mysqli_real_escape_string($sql['link'], $_POST['contact'][$id])."'
				WHERE `id` = '".mysqli_real_escape_string($sql['link'], $id)."'";
				if (mysqli_query($sql['link'], $qry)) {
					$msg = 's001';
				}
				else {
					$msg = 'e002';
				}
			}
			
			if (!empty($_POST['description'][0])) {
				$qry = "INSERT INTO `".$sql['database']."`.`".$sql['table_id']."`
				SET
				`parent_id` = ".$_POST['id'].",
				`time` = '".date('H:i:s', strtotime($_POST['time'][0]))."',
				`description` = '".mysqli_real_escape_string($sql['link'], $_POST['description'][0])."',
				`contact` = '".mysqli_real_escape_string($sql['link'], $_POST['contact'][0])."'";
				if (mysqli_query($sql['link'], $qry)) {
					$msg = 's001';
				}
				else $msg = 'e002';
			}
		}
		else $msg = 'e001';
		echo mysqli_error($sql['link']);
	}
	//add
	else {
		$qry = "INSERT INTO `".$sql['database']."`.`".$sql['table_i']."`
		SET
		`date` = '".date('Y-m-d', strtotime($_POST['date']))."',
		`road` = '".mysqli_real_escape_string($sql['link'], $_POST['road'])."',
		`location` = '".mysqli_real_escape_string($sql['link'], $_POST['location'])."',
		`scenario` = '".mysqli_real_escape_string($sql['link'], $_POST['scenario'])."'";
		if (mysqli_query($sql['link'], $qry)) {
			
			$id = mysqli_insert_id($sql['link']);
			
			$qry = "INSERT INTO `".$sql['database']."`.`".$sql['table_id']."`
			SET
			`parent_id` = ".$id.",
			`time` = '".date('H:i:s', strtotime($_POST['time'][0]))."',
			`description` = '".mysqli_real_escape_string($sql['link'], $_POST['description'][0])."',
			`contact` = '".mysqli_real_escape_string($sql['link'], $_POST['contact'][0])."'";
			if (mysqli_query($sql['link'], $qry)) {
				$msg = 's001';
			}
			else $msg = 'e002';
		}
		else $msg = 'e001';
		echo mysqli_error($sql['link']);
	}
	
	//decide return to main page or continue editing
	if (!empty($_POST['add'])) $p = 'i';
}

?>