<?php
//include database gegevens
include('db.inc.php');

//connect to database
$db['link'] = mysqli_connect($sql['server'], $sql['user'], $sql['password'], $sql['database']);
?>