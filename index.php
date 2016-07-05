<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/


include('functions/getuser.fct.php');
//check login
if (getuser() === FALSE) {
	header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/login.php');
}

include('db.inc.php');

//connect to database
$sql['link'] = mysqli_connect($sql['server'], $sql['user'], $sql['password']);

//save new content
if (!empty($_GET['s']) && file_exists('includes/'.urlencode($_GET['s']).'_save.inc.php')) include('includes/'.urlencode($_GET['s']).'_save.inc.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>OVM logging systeem</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<link rel="stylesheet" type="text/css" href="print.css" media="print" />
<link rel="stylesheet" href="jquery/jquery-ui.css" />
<script src="jquery/jquery-1.8.3.js"></script>
<script src="jquery/jquery-ui.js"></script>
<script src="jquery/jquery.ui.datepicker-nl.js"></script>
<script src="jquery/jquery-ui-timepicker-addon.js"></script>
<script src="jquery/jquery-ui-timepicker-addon-nl.js"></script>
</head>
<body>

<div id="container">
<div id="content">
<div id="logo"></div>
<p class="noprint">Welkom <b><?php echo getuser('name'); ?></b> | <a href="login.php" tabindex="-1">wissel gebruiker</a></p>

<?php
//include messages
include('includes/messages.inc.php');

if (empty($p)) $p = $_GET['p'];
if (empty($p)) include ('includes/main.inc.php');
elseif (file_exists('includes/'.urlencode($p).'.inc.php')) include('includes/'.urlencode($p).'.inc.php');
else include ('includes/404.inc.php');
?>
</div>
</div>

</body>
</html>
