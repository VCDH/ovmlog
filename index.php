<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

include('db.inc.php');

//connect to database
$sql['link'] = mysqli_connect($sql['server'], $sql['user'], $sql['password']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>OVM logging systeem</title>
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>

<?php
if (empty($_GET['p'])) include ('includes/main.inc.php');
elseif (file_exists('includes/'.urlencode($_GET['p']).'.inc.php')) include('includes/'.urlencode($_GET['p']).'.inc.php');
else include ('includes/404.inc.php');
?>

</body>
</html>
