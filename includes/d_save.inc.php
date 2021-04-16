<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2021
*/

if (($_GET['s'] == 'd') && !empty($_POST)) {
	//check if entry is not empty
	if (empty($_POST['entry'])) {
		header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php', TRUE, 303);
		exit;
	}
	
	//save entry
	$qry = "INSERT INTO `".$sql['database']."`.`".$sql['table_d']."`
	SET
	`datetime` = NOW(),
	`description` = '".mysqli_real_escape_string($sql['link'], $_POST['entry'])."',
	`user_id_create` = '".getuser()."',
	`user_id_edit` = '".getuser()."'";
	if (mysqli_query($sql['link'], $qry)) $msg = null;
	else $msg = 'e001';

	//fix browser back button
	header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php?msg=' . $msg, TRUE, 303);
	exit;
}
?>