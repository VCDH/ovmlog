<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2016, 2022
*/

//controleer of mag bekijken
if (permissioncheck('bewerk') !== true) {
	echo 'niet toegestaan';
	exit;
}
?>

<h2>Werkzaamheden en evenementen - historie</h2>
<p><a href="?">&laquo; terug</a> | <?php
if ($_GET['dvmx'] == '1') {
	echo '<a href="?p=p_hist&amp;dvmx=0">toon alles</a>';
}
else {
	echo '<a href="?p=p_hist&amp;dvmx=1">toon alleen DVM-Exchange</a>';
}
?>
</p>

<?php

include 'functions/generic.fct.php';

$qry = "SELECT `".$sql['table_p']."`.`id`, `datetime_start`, `datetime_end`, `road`, `location`, `scenario`, `type`, `name`, `spare`, `".$sql['table_users']."`.`username` AS `assigned`
	FROM `".$sql['database']."`.`".$sql['table_p']."`
	LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."`
	ON `".$sql['table_users']."`.`id` = `".$sql['table_p']."`.`user_id_assigned` 
	WHERE `datetime_end` < NOW() ";
if ($_GET['dvmx'] == '1') {
	$qry .= "AND `scenario` = 'DVM-Exchange'";
}
$qry .= "ORDER BY `datetime_start` DESC";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th></th><th>start</th><th>eind</th><th>locatie</th><th title="reserve">res</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		display_planned_tablerow($row, TRUE);
	}
	echo '</table>';
}
else {
	?>
	<p>Geen werkzaamheden of evenementen.</p>
	<?php
}
?>