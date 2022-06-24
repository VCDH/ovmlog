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
//include database gegevens
include('dbconnect.inc.php');

//functie om nieuw wachtwoord te genereren
function genereer_wachtwoord($len) {
	$tekens = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$wachtwoord = '';
	for ($i = 0; $i < $len; $i++) {
		$start = mt_rand(0, strlen($tekens) - 1);
		$wachtwoord = $wachtwoord . substr($tekens, $start, 1);
	}
	return $wachtwoord;
} 

//functie om mail via smtp te verzenden
function smtp_mail($to, $subject, $body) {
    date_default_timezone_set('Europe/Amsterdam');

    require_once('PHPMailer-phpmailer-5.2.0/class.phpmailer.php');
    //include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
    $mail             = new PHPMailer();
    include('mail.cfg.php');
    $mail->Subject    = $subject;
    //$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->MsgHTML($body);
    $mail->AddAddress($to);

    if(!$mail->Send()) {
        return "Mailer Error: " . $mail->ErrorInfo;
    } 
    else {
        return TRUE;
    }
}

//als formulier verzonden
if (!empty($_POST['username']) && !empty($_POST['password'])) {
    //stel karakterset in voor mysqli_real_escape_string
    mysqli_set_charset($db['link'], 'latin1');
    //bereken hash van wachtwoord
    $wachtwoord = strtolower(hash('sha256', $_POST['password']));
    //query om tabel te lezen
    $sql = "SELECT
    `id`
    FROM `".$db['prefix']."users`
    WHERE `email` = '" . mysqli_real_escape_string($db['link'], $_POST['username']) . "'
    AND `password` = '" . mysqli_real_escape_string($db['link'], $wachtwoord) . "'
    AND `disabled` = 0
    LIMIT 1"; 
	//voer query uit
    $result = mysqli_query($db['link'], $sql);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_row($result);
        $cookie['id'] = $row[0];
        $cookie['token'] = $wachtwoord;
        //zet cookie
        setcookie('login', serialize($cookie), time() + 60*60*24*7*2, '/');
        //login is gelukt
        $login_correct = TRUE;
		//redirect
		header('Location:http://'.$_SERVER["SERVER_NAME"].substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/')).'/index.php');
    }
    //wachtwoord niet correct
    else {
        $login_error = TRUE;
    } 
}
//verwerk registratie
elseif (!empty($_POST['name']) && !empty($_POST['email'])) {
	//check email address
	$organisation = substr($_POST['email'], strpos($_POST['email'], '@')+1);
	//query om tabel te lezen
    $sql = "SELECT
    `id`
    FROM `".$db['prefix']."organisations`
    WHERE `email` = '" . mysqli_real_escape_string($db['link'], $organisation) . "'
	AND `allowsignup` = TRUE
    LIMIT 1"; 
	//voer query uit
    $result = mysqli_query($db['link'], $sql);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_row($result);
        $organisation_id = $row[0];
		//genereer nieuw wachtwoord
		$wachtwoord = genereer_wachtwoord(8);
		//stel afzender en bericht op
		$onderwerp = 'Wachtwoord voor ' . $_SERVER['SERVER_NAME'];
		$bericht = '<html><body><p>Beste '.htmlspecialchars($_POST['name']).',</p><p>Jij (of iemand anders voor je) hebt je geregistreerd op de website ' . $_SERVER['SERVER_NAME'] . '. Je inloggegevens zijn als volgt:</p><p>Gebruikersnaam: '.htmlspecialchars($_POST['email']).'<br>Wachtwoord: '.$wachtwoord.'</p><p>Desgewenst kun je dit wachtwoord na aanmelden op de website wijzigen in een zelfgekozen wachtwoord.</p></body></html>';
		//genereer hash
		$wachtwoord = hash('sha256', $wachtwoord);
		//query om nieuwe gebruiker in te voegen
		//als gebruikersnaam al bestaat zal query vanzelf mislukken, omdat `gebruikersnaam` een unieke sleutel is
		$sql = "INSERT INTO `".$db['prefix']."users`
		SET
			`name` = '" . mysqli_real_escape_string($db['link'], $_POST['name']) . "',
			`password` = '" . mysqli_real_escape_string($db['link'], $wachtwoord) . "',
			`email` = '" . mysqli_real_escape_string($db['link'], $_POST['email']) . "',
			`phone` = '" . mysqli_real_escape_string($db['link'], $_POST['phone']) . "',
			`organisation` = '" . mysqli_real_escape_string($db['link'], $organisation_id) . "' ";
		//voer query uit
		if (mysqli_query($db['link'], $sql)) {
			//stuur email
			$verzonden = smtp_mail($_POST['email'], $onderwerp, $bericht);
		}
		else {
			//er is geen gebruiker
			$gebruikersnaam_fout = TRUE;
		}
    }
    //geen toegestane organisatie
    else {
        $organisation_error = TRUE;
    } 
}
//verwerk nieuw wachtwoord
//verwerk registratie
elseif (($_GET['do'] == 'lostpass') && !empty($_POST['email'])) {
	//query om tabel te lezen
    $sql = "SELECT
    `id`, `username`, `email`
    FROM `".$db['prefix']."users`
    WHERE `email` = '" . mysqli_real_escape_string($db['link'], $_POST['email']) . "'
    AND `disabled` = 0
    LIMIT 1"; 
	//voer query uit
    $result = mysqli_query($db['link'], $sql);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_row($result);
        $user_id = $row[0];
		//genereer nieuw wachtwoord
		$wachtwoord = genereer_wachtwoord(8);
		//stel afzender en bericht op
		$onderwerp = 'Wachtwoord voor ' . $_SERVER['SERVER_NAME'];
$bericht = '<html><body><p>Beste '.htmlspecialchars($row[1]).',</p><p>Jij (of iemand anders voor je) hebt een nieuw wachtwoord aangevraagd voor de website ' . $_SERVER['SERVER_NAME'] . '. Je nieuwe inloggegevens zijn als volgt:</p><p>Gebruikersnaam: '.htmlspecialchars($row[2]).'<br>Wachtwoord: '.$wachtwoord.'</p><p>Desgewenst kun je dit wachtwoord na aanmelden op de website wijzigen in een zelfgekozen wachtwoord.</p></body></html>';
		//genereer hash
		$wachtwoord = hash('sha256', $wachtwoord);
		//query om nieuwe gebruiker in te voegen
		//als gebruikersnaam al bestaat zal query vanzelf mislukken, omdat `gebruikersnaam` een unieke sleutel is
		$sql = "UPDATE `".$db['prefix']."users`
		SET
			`password` = '" . mysqli_real_escape_string($db['link'], $wachtwoord) . "'
			WHERE `id` = '".$user_id."'";
		//voer query uit
		if (mysqli_query($db['link'], $sql)) {
			//stuur email
			$verzonden = smtp_mail($_POST['email'], $onderwerp, $bericht, $afzender);
		}
    }
    //email niet gevonden
    else {
        $email_fout = TRUE;
    } 
}

?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>OVM logging systeem - aanmelden</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div id="container">
<div id="content">
<div id="logo"></div>
    <?php
	if (($_GET['do'] == 'lostpass') && ($verzonden !== TRUE)) {
		?>
        <h1>Wachtwoord vergeten</h1>
        <?php
		if ($email_fout === TRUE) {
            echo '<p class="error">Er is geen account met dit emailadres.</p>';
        }
		if ($verzonden === FALSE) {
            echo '<p class="error">Er kon geen e-mail met wachtwoord worden verzonden. Probeer het later nogmaals. Neem contact op met de beheerder van deze website als het probleem zich blijft voordoen. '.htmlspecialchars($verzonden).'</p>';
        }
        ?>
        <p>Vul hieronder je e-mailadres in en er zal een nieuw wachtwoord naar het opgegeven adres worden gezonden.</p>
        <form method="post">
        <table>
        <tr><td>E-mailadres:</td><td><input type="text" name="email"></td></tr>
        <tr><td></td><td><input type="submit" value="Nieuw wachtwoord aanvragen"></td></tr>
        </table>
        </form> 
        <p><a href="?">&laquo; terug</a></p>
        <?php 
		
	}
	else {
		?>
        <h1>Aanmelden</h1>
        
        <?php
        if ($login_error === TRUE) {
            echo '<p class="error">De gebruikersnaam/wachtwoord combinatie bestaat niet.</p>';
        }
		if ($verzonden === TRUE) {
            echo '<p class="success">Er is een e-mail met wachtwoord gestuurd naar het opgegeven e-mailadres. Na aanmelden kan het gegenereerde wachtwoord desgewenst worden gewijzigd.</p>';
        }
        ?>
        
        <form method="post">
        <table>
        <tr><td>E-mailadres:</td><td><input type="text" name="username"></td></tr>
        <tr><td>Wachtwoord:</td><td><input type="password" name="password"></td></tr>
        <tr><td></td><td><input type="submit" value="Login"></td></tr>
        </table>
        </form> 
        <p><a href="?do=lostpass">Wachtwoord vergeten</a></p>
        <?php 
	}
	?>
</div>
</div>
</body>
</html>
