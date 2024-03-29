<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2021-2022
*/

//controleer of mag opslaan
if (permissioncheck('bewerk') !== true) {
	echo 'niet toegestaan';
	exit;
}

//ovkvd/piket
if (($_GET['s'] == 'd') && ($_GET['do'] == 'ovkvd')) {
	//prepare json
	$json = array(
		'date'=> date('Ymd'),
		'ovkvd' => $_POST['ovkvd'],
		'piket' => $_POST['piket']
	);
	$json = json_encode($json);
	//store json
	file_put_contents('ovkvdpiket.json', $json);
	//goto main page
	header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php', TRUE, 303);
	exit;
}

//sticky/unsticky
if (($_GET['s'] == 'd') && (($_GET['do'] == 'sticky') || ($_GET['do'] == 'unsticky'))) {
	$sticky = 'FALSE';
	if ($_GET['do'] == 'sticky') {
		$sticky = 'TRUE';
	}
	$qry = "UPDATE `".$sql['database']."`.`".$sql['table_d']."`
	SET
	`sticky` = " . $sticky . ",
	`user_id_edit` = '".getuser()."'
	WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'";
	mysqli_query($sql['link'], $qry);
	//goto main page
	header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php', TRUE, 303);
	exit;
}

//review/unreview
if (($_GET['s'] == 'd') && (($_GET['do'] == 'review') || ($_GET['do'] == 'unreview'))) {
	//check if entry exists
	$qry = "SELECT `id`, `datetime` FROM `".$sql['database']."`.`".$sql['table_d']."`
	WHERE
	`id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res) != 1) {
		header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php?msg=e001', TRUE, 303);
		exit;
	}
	$row = mysqli_fetch_assoc($res);
	//get date from datetime
	$date = date('d-m-Y', strtotime($row['datetime']));
	
	//decide state
	$review = 'FALSE';
	if ($_GET['do'] == 'review') {
		$review = 'TRUE';
	}
	$qry = "UPDATE `".$sql['database']."`.`".$sql['table_d']."`
	SET
	`review` = " . $review . ",
	`user_id_edit` = '".getuser()."'
	WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'";
	mysqli_query($sql['link'], $qry);
	//goto main page
	header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php?p=d_view&date='.$date, TRUE, 303);
	exit;
}

//save and insert
if (($_GET['s'] == 'd') && !empty($_POST)) {
	//check if entry is not empty
	if (empty($_POST['entry'])) {
		header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php', TRUE, 303);
		exit;
	}

	//save
	if (is_numeric($_GET['id'])) {
		//check if entry exists
		$qry = "SELECT `id`, `datetime`, `user_id_create` FROM `".$sql['database']."`.`".$sql['table_d']."`
		WHERE
		`id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'";
		$res = mysqli_query($sql['link'], $qry);
		if (mysqli_num_rows($res) != 1) {
			header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php?msg=e001', TRUE, 303);
			exit;
		}
		$row = mysqli_fetch_assoc($res);
		//get date from datetime
		$date = date('d-m-Y', strtotime($row['datetime']));
		//check user
		/*if ($row['user_id_create'] != getuser()) {
			header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php?p=d_view&date='.$date.'&msg=e001', TRUE, 303);
			exit;
		}*/
		//check if entry is not empty
		if (empty($_POST['entry'])) {
			header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php?p=d_view&date='.$date.'&msg=e007', TRUE, 303);
			exit;
		}
		//set new datetime
		$datetime = '';
		if (preg_match('/^[012]?[0-9]{1}:[0-5]{1}[0-9]{1}$/', $_POST['time'])) {
			if (strlen($_POST['time']) == 4) {
				$_POST['time'] = '0' . $_POST['time'];
			}
			$datetime = "`datetime` = '" . date('Y-m-d', strtotime($row['datetime'])) . " " . $_POST['time'] .":00',";
		}
		//update entry
		$qry = "UPDATE `".$sql['database']."`.`".$sql['table_d']."`
		SET
		" . $datetime . "
		`description` = '".mysqli_real_escape_string($sql['link'], $_POST['entry'])."',
		`user_id_edit` = '".getuser()."'
		WHERE `id` = '".$row['id']."'";
		if (mysqli_query($sql['link'], $qry)) $msg = 's001';
		else $msg = 'e001';
		header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php?p=d_view&date='.$date.'&msg=.$msg', TRUE, 303);
		exit;
	}
	//new item
	else {
		//decide time
		$_POST['time'] = trim($_POST['time']);
		if (preg_match('/^[012]?[0-9]{1}:[0-5]{1}[0-9]{1}$/', $_POST['time'])) {
			if (strlen($_POST['time']) == 4) {
				$_POST['time'] = '0' . $_POST['time'];
			}
			$datetime = '\'' . date('Y-m-d') . ' ' . $_POST['time'] . ':00\'';
		}
		else {
			$datetime = 'NOW()';
		}
		//decide sticky
		$sticky = 'FALSE';
		if ($_POST['sticky'] == 'true') {
			$sticky = 'TRUE';
		}

		//save entry
		$qry = "INSERT INTO `".$sql['database']."`.`".$sql['table_d']."`
		SET
		`datetime` = " . $datetime . ",
		`description` = '".mysqli_real_escape_string($sql['link'], $_POST['entry'])."',
		`sticky` = " . $sticky . ",
		`user_id_create` = '".getuser()."',
		`user_id_edit` = '".getuser()."'";
		if (mysqli_query($sql['link'], $qry)) $msg = null;
		else $msg = 'e001';

		//fix browser back button
		header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php?msg=' . $msg, TRUE, 303);
		exit;
	}
}
?>