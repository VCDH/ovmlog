<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/
exit;

include('db.inc.php');

//connect to database
$sql['link'] = mysqli_connect($sql['server'], $sql['user'], $sql['password'], $sql['database']);

$qry = "SELECT * FROM `".$sql['table_e']."`";
$res = mysqli_query($sql['link'], $qry);
while ($data = mysqli_fetch_assoc($res)) {
    $qry2 = "INSERT INTO `".$sql['table_p']."` SET
    `type` = 'e',
    `datetime_start` = '".mysqli_real_escape_string($sql['link'], $data['datetime_start'])."',
    `datetime_end` = '".mysqli_real_escape_string($sql['link'], $data['datetime_end'])."',
    `name` = '".mysqli_real_escape_string($sql['link'], $data['name'])."',
    `description` = '".mysqli_real_escape_string($sql['link'], $data['description'])."',
    `scenario` = '".mysqli_real_escape_string($sql['link'], $data['scenario'])."',
    `user_id_create` = '".mysqli_real_escape_string($sql['link'], $data['user_id_create'])."',
    `user_id_edit` = '".mysqli_real_escape_string($sql['link'], $data['user_id_edit'])."'";
    mysqli_query($sql['link'], $qry2);
}
echo mysqli_error($sql['link']);
?>