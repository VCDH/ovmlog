<?php
/*
	ovmlog - logtool voor operationeel verkeersmanagement
	Copyright (C) 2013, 2022 Gemeente Den Haag, Netherlands
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
	header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/login.php');
}

include('db.inc.php');

//connect to database
$sql['link'] = mysqli_connect($sql['server'], $sql['user'], $sql['password']);

//save new content
if (!empty($_GET['s']) && file_exists('includes/'.urlencode($_GET['s']).'_save.inc.php')) include('includes/'.urlencode($_GET['s']).'_save.inc.php');

?>
<!DOCTYPE html>
<html>
<head>
<title>OVM logging systeem</title>
<script src="jquery/jquery.min.js"></script>
<script src="jquery/jquery-ui.min.js"></script>
<script src="jquery/jquery.ui.datepicker-nl.js"></script>
<script src="jquery/jquery-ui-timepicker-addon.js"></script>
<script src="jquery/jquery-ui-timepicker-addon-nl.js"></script>
<link rel="stylesheet" href="jquery/jquery-ui.min.css" />
<link rel="stylesheet" type="text/css" href="style.css" />
<link rel="stylesheet" type="text/css" href="print.css" media="print" />
</head>
<body>

<div id="container">
<div id="content">
<div id="logo"></div>
<p class="noprint">Welkom <b><?php echo getuser('name'); ?></b> | <a href="account.php" tabindex="-2">account</a> | <a href="logout.php" tabindex="-1">afmelden</a></p>

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
