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
session_start();
include_once('logincheck.inc.php');
//redirect if not logged in
if (!logincheck()) {
	header('Location:http://'.$_SERVER["SERVER_NAME"].substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/')).'/index.php');
    exit;
}
//include database gegevens
include('dbconnect.inc.php');

//verwerk wachtwoordwijziging
if (($_GET['do'] == 'changepass') && (!empty($_POST))) {
    //controleer of nieuwe wachtwoorden gelijk
    if ($_POST['new_password1'] == $_POST['new_password2']) {
        //controleer wachtwoordlengte
		if (strlen($_POST['new_password1']) >= 8) {
			//stel karakterset in voor mysqli_real_escape_string
			mysqli_set_charset($db['link'], 'latin1');
			//bereken hash van oud wachtwoord
			$wachtwoord = hash('sha256', $_POST['old_password']);
			//query om rij te selecteren
			$sql = "SELECT
			`id`
			FROM `".$db['prefix']."users`
			WHERE `id` = '" . mysqli_real_escape_string($db['link'], $_SESSION['id']) . "'
			AND `password` = '" . mysqli_real_escape_string($db['link'], $wachtwoord) . "'
			LIMIT 1";
			//voer query uit
			$result = mysqli_query($db['link'], $sql);
			if (mysqli_num_rows($result) != 1) {
				//oud wachtwoord niet correct
				$oud_wachtwoord_fout = TRUE;
			}
			else {
				//wachtwoord correct, zet nieuw wachtwoord
				//bereken hash van nieuw wachtwoord
				$wachtwoord = hash('sha256', $_POST['new_password1']);
				//query om rij aan te passen
				$sql = "UPDATE `".$db['prefix']."users`
				SET `password` = '" . mysqli_real_escape_string($db['link'], $wachtwoord) . "'
				WHERE `id` = '" . mysqli_real_escape_string($db['link'], $_SESSION['id']) . "'";
				//voer query uit
				$wachtwoord_gewijzigd = mysqli_query($db['link'], $sql);
				//plaats cookie opnieuw om te voorkomen dat gebruiker uitgelogd is
				if ($wachtwoord_gewijzigd === TRUE) {
					$cookie['id'] = $_SESSION['id'];
					$cookie['token'] = $wachtwoord;
					//zet cookie
					setcookie('login', serialize($cookie), time() + 60*60*24*7*2, '/');
				}
			}
		}
		else {
			//wachtwoord niet lang genoeg
			$wachtwoord_lengte = TRUE;
		}
    }
    else {
        //nieuwe wachtwoorden niet gelijk
        $nieuw_wachtwoord_fout = TRUE;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>OVM logging systeem - Account</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div id="container">
<div id="content">
<div id="logo"></div>
<p><a href="index.php">&laquo; Overzicht</a></p>

<div id="content">
    <?php 
	if (($_GET['do'] == 'changepass') && ($wachtwoord_gewijzigd !== TRUE)) {
		?>
		<h1>Wijzig wachtwoord</h1>

		<?php
		if ($nieuw_wachtwoord_fout === TRUE) {
			echo '<p class="error">De ingevulde nieuwe wachtwoorden zijn niet gelijk.</p>';
		}
		if ($oud_wachtwoord_fout === TRUE) {
			echo '<p class="error">De oude wachtwoord is niet juist.</p>';
		}
		if ($wachtwoord_lengte === TRUE) {
			echo '<p class="error">Het nieuwe wachtwoord moet minstens 8 tekens lang zijn.</p>';
		}
		?>
		
		<form method="post">
		<table>
		<tr><td>Oud wachtwoord:</td><td><input type="password" name="old_password"></td></tr>
		<tr><td>Nieuw wachtwoord:</td><td><input type="password" name="new_password1"></td></tr>
		<tr><td>Herhaal wachtwoord:</td><td><input type="password" name="new_password2"></td></tr>
		<tr><td></td><td><input type="submit" value="Wijzig wachtwoord"> <a href="?">Annuleren</a></td></tr>
		</table>
		</form>
		<?php
	}
	//main page
	else {
		if ($wachtwoord_gewijzigd === TRUE) {
			echo '<p class="succes">Wachtwoord gewijzigd!</p>';
		}
		if ($userprofile_gewijzigd === TRUE) {
			echo '<p class="succes">Gebruikersgegevens gewijzigd!</p>';
		}
		?>
        <h1>Account</h1>
        <p><a href="?do=changepass">Wachtwoord wijzigen</a></p>
        <?php
	}
	?>
</div>
</body>
</html>
