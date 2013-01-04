<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

include('db.inc.php');
include('cookie.inc.php');
include('functions/getuser.fct.php');

//log user out if logged in
if ((getuser() !== FALSE) && !empty($_COOKIE[$cookie['name']])) {
	if (setcookie($cookie['name'], '', time()-3600)) {
		$msg = 's002';
	}
	else {
		$msg = 'e005';
		exit;
	}
}

//connect to database
$sql['link'] = mysqli_connect($sql['server'], $sql['user'], $sql['password']);

//attempt login
if (!empty($_POST['username'])) {
	$qry = "SELECT `id` 
		FROM `".$sql['database']."`.`".$sql['table_users']."`
		WHERE `username` LIKE '".mysqli_real_escape_string($sql['link'], $_POST['username'])."'";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res) == 1) {
		$data = mysqli_fetch_row($res);
		//set cookie
		$value = serialize(array($data[0]));
		
		if (setcookie($cookie['name'], $value, time()+$cookie['expire'])) {
			header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\'));
		}
		else {
			$login = FALSE;
			$msg = 'e004';
		}
	}
	else {
		$login = FALSE;
		$msg = 'e003';
	}
}
else $login = FALSE;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>OVM logging systeem - aanmelden</title>
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>

<div id="container">
<div id="content">
<div id="logo"></div>
<?php
//include messages
include('includes/messages.inc.php');

if ($login === FALSE) {
	?>
	<h1>aanmelden</h1>
	<form action="login.php" method="post">
	
	<table>
		<tr>
			<td><label for="username">gebruikersnaam</label></td>
			<td>
			<?php
			//get users from database
			$qry = "SELECT `username` 
				FROM `".$sql['database']."`.`".$sql['table_users']."`
				ORDER BY `username`";
			$res = mysqli_query($sql['link'], $qry);
			if (mysqli_num_rows($res)) {
				echo '<select name="username" id="username">';
				while ($row = mysqli_fetch_row($res)) {
					echo '<option value="'.htmlspecialchars($row[0]).'">'.htmlspecialchars($row[0]).'</option>';
				}
				echo '</select>';
			}
			else echo 'geen gebruikers gevonden';
			?>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="submit" value="Aanmelden" /></td>
		</tr>
	</table>
	
	</form>
	<?php
}
?>
</div>
</div>

</body>
</html>
