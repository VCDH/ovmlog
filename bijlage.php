<?php 
/*
 	scenariobrowser - viewer en editor voor verkeersmanagementscenario's
    Copyright (C) 2016-2019 Gemeente Den Haag, Netherlands
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

include('functions/getuser.fct.php');
//check login
if (getuser() === FALSE) {
	http_response_code(403); //forbidden
	exit;
}

include('db.inc.php');
//connect to database
$sql['link'] = mysqli_connect($sql['server'], $sql['user'], $sql['password'], $sql['database']);

//content types
$content_types = array(
'.doc' => 'application/msword',
'.docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
'.xls' => 'application/vnd.ms-excel',
'.xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
'.pdf' => 'application/pdf',
'.jpg' => 'image/jpeg',
'.png' => 'image/png',
'.gif' => 'image/gif',
'.bmp' => 'image/bmp',
'.txt' => 'text/plain',
'.csv' => 'text/csv',
'.zip' => 'application/zip',
'.eml' => 'message/rfc822',
'.msg' => 'application/vnd.ms-outlook'
);

$max_filesize = 180*1024*1024; //bytes

include_once('convertfilesize.fct.php');

//download bestand
if ($_GET['do'] == 'getfile') {
	//controleer of er een bestand is
	$qry = "SELECT `bestand`, `grootte`, `bestandsnaam` 
	FROM `".$sql['table_bijlagen']."`
	WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res)) {
		$row = mysqli_fetch_row($res);
		//bepaal extensie
		$filetype = strtolower(substr($row[0], strrpos($row[0], '.')));
		$file = 'attachments/'.strtoupper(substr($row[0], 0, 1)).'/'.$row[0];
		if (file_exists($file)) {
			//geef bestand
			header('Content-Description: File Transfer');
			header('Content-Type: '.((array_key_exists($filetype, $content_types)) ? $content_types[$filetype] : 'application/octet-stream'));
			header('Content-Disposition: attachment; filename='.sprintf('"%s"', addcslashes(basename($row[2]), '"\\'))); 
			header('Content-Transfer-Encoding: binary');
			header('Connection: Keep-Alive');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . $row[1]);
			ob_clean();
			readfile($file);
		}
		else {
			http_response_code(404); //not found
		}
	}
	else {
		http_response_code(404); //not found
	}
	exit;
}

elseif ($_GET['do'] == 'delete') {
	//bepaal of verwijderd mag worden
	$qry = "SELECT `id`, `bestand`
	FROM `".$sql['table_bijlagen']."`
	WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res) == 1) {
		$row = mysqli_fetch_row($res);
		//verwijder uit database
		$qry = "DELETE FROM `".$sql['table_bijlagen']."`
		WHERE `id` = '".$row[0]."'";
		mysqli_query($sql['link'], $qry);
		//controleer of het bestand nog gebruikt wordt
		$qry = "SELECT `id`
		FROM `".$sql['table_bijlagen']."`
		WHERE `bestand` = '".mysqli_real_escape_string($sql['link'], $row[1])."'";
		$res = mysqli_query($sql['link'], $qry);
		if (mysqli_num_rows($res) === 0) {
			//verwijder bestand
			echo $file = 'attachments/'.strtoupper(substr($row[1], 0, 1)).'/'.$row[1];
			@unlink($file);
		}
	}
	exit;
}

elseif ($_GET['do'] == 'setarchive') {
	//controleer of geldig verzoek
	if (!in_array($_GET['value'], array('0', '1'))) {
		http_response_code(400); //bad request
		exit;
	}
	//zet nieuwe status
	$qry = "UPDATE `".$sql['table_bijlagen']."` SET
	`archief` = '".mysqli_real_escape_string($sql['link'], $_GET['value'])."'
	WHERE `id` = '".$row[0]."'";
	mysqli_query($sql['link'], $qry);
	exit;
}

elseif ($_GET['do'] == 'upload') {
	//check if files
	if (empty($_FILES['files']['name'])) {
		http_response_code(400); //bad request
		exit;
	}
	
	$files = array('files' => array());
	
	//handle uploads
	foreach ($_FILES["files"]["error"] as $key => $error) {
		$name = $_FILES["files"]["name"][$key];
		$tmp_name = $_FILES["files"]["tmp_name"][$key];
		$md5 = md5_file($tmp_name);
		$size = $_FILES["files"]["size"][$key];
		$filetype = strtolower(substr($name, strrpos($name, '.')));
		if ($error == UPLOAD_ERR_OK) {
			//check filesize
			if ($size >= $max_filesize) {
				$files['files'][] = array('name' => $name, 'size' => $size, 'error' => 'Bestand is te groot, maximumgrootte ' . convertfilesize($max_filesize));
			}
			//check filetype
			elseif (!array_key_exists($filetype, $content_types)) {
				$files['files'][] = array('name' => $name, 'size' => $size, 'error' => 'Bestandstype niet toegestaan.');
			}
			//should probably check if the parent id exists
			else {
				//insert in database
				$qry = "INSERT INTO `".$sql['table_bijlagen']."` SET
				`parent_id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."',
				`datum` = NOW(),
				`user_id` = '".getuser()."',
				`bestandsnaam` = '".mysqli_real_escape_string($sql['link'], $name)."',
				`grootte` = '".$size."',
				`bestand` = '".$md5.$filetype."'";
				mysqli_query($sql['link'], $qry);
				$target_file = 'attachments/'.strtoupper(substr($md5, 0, 1)).'/'.$md5.$filetype;
				if (!file_exists($target_file)) {
					//move original file
					move_uploaded_file($tmp_name, $target_file);
				}
				$files['files'][] = array('name' => $name, 'size' => $size, 'url' => $target_file);
			}
		}
		else {
			$files['files'][] = array('name' => $name, 'size' => $size, 'error' => 'Kan bestand niet opslaan');
		}
	}
	header('Content-type: application/json');
	echo json_encode($files, JSON_FORCE_OBJECT);
	exit;
}

elseif ($_GET['do'] == 'getlist') {
	//selecteer bijlagen
	$files = array();
	$qry = "SELECT `".$sql['table_bijlagen']."`.`id`, `".$sql['table_bijlagen']."`.`datum`, `".$sql['table_users']."`.`username`, `".$sql['table_bijlagen']."`.`bestandsnaam`, `".$sql['table_bijlagen']."`.`grootte`
	FROM `".$sql['table_bijlagen']."`
	LEFT JOIN `".$sql['table_users']."`
	ON `".$sql['table_bijlagen']."`.`user_id` = `".$sql['table_users']."`.`id`
	WHERE `".$sql['table_bijlagen']."`.`parent_id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'
	ORDER BY `".$sql['table_bijlagen']."`.`bestandsnaam`";
	$res = mysqli_query($sql['link'], $qry);
	echo mysqli_error($sql['link']);
	while ($row = mysqli_fetch_row($res)) {
		$files[] = array('id' => $row[0],
		'datum' => $row[1],
		'user' => $row[2],
		'bestandsnaam' => $row[3],
		'grootte' => convertfilesize($row[4]),
		'archief' => $row[5]);
	}
	header('Content-type: application/json');
	echo json_encode($files);
	exit;
}
?>