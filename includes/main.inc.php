<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/
?>

<h1>Op dit moment</h1>
<h2>Incidenten</h2>
<p><a href="?p=i">nieuw</a> | <a href="?p=i_hist">historie</a></p>

<?php
$qry = "SELECT `id`, `date` 
	FROM `".$sql['database']."`.`".$sql['table_i']."`
	WHERE `open` = 1
	ORDER BY `date` DESC";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th>datum</th><th>tijd</th><th>omschrijving</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr><td><a href="?p=i&amp;id='.$row[0].'">'.$row[1].'</a></td>';
		$qry2 = "SELECT `time`, `description`
		FROM `".$sql['database']."`.`".$sql['table_id']."`
		WHERE `parent_id` = ".$row[0]."
		ORDER BY `time`";
		$res2 = mysqli_query($sql['link'], $qry2);
		if (mysqli_num_rows($res2)) {
			while ($row2 = mysqli_fetch_row($res2)) {
				//nog iets toevoegen van string afbreken na bepaalde lengte
				echo '<td>'.$row2[0].'</td><td class="expand">'.htmlspecialchars(str_replace('<br />', ' ', $row2[1])).'</td></tr><tr class="nogrid"><td>&nbsp;</td>';
			}
		}
		echo '<td>&nbsp;</td><td>&nbsp;</td></tr>';
	}
echo '</table>';
}
else {
	?>
	<p>Geen openstaande incidenten.</p>
	<?php
}
?>

<h2>Werkzaamheden</h2>
<p><a href="?p=w">nieuw</a> | <a href="?p=w_hist">historie</a></p>

<?php
$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `description` 
	FROM `".$sql['database']."`.`".$sql['table_w']."`
	WHERE `datetime_end` > NOW()
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th>start</th><th>eind</th><th>omschrijving</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr><td><a href="?p=w_view&amp;id='.$row[0].'">'.$row[1].'</a></td><td>'.$row[2].'</td><td class="expand">'.htmlspecialchars($row[3]).'</td></tr>';
	}
echo '</table>';
}
else {
	?>
	<p>Geen werkzaamheden.</p>
	<?php
}
?>

<h2>Evenementen</h2>
<p><a href="?p=e">nieuw</a> | <a href="?p=e_hist">historie</a></p>

<?php
$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `name` 
	FROM `".$sql['database']."`.`".$sql['table_e']."`
	WHERE `datetime_end` > NOW()
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th>start</th><th>eind</th><th>omschrijving</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr><td><a href="?p=e_view&amp;id='.$row[0].'">'.$row[1].'</a></td><td>'.$row[2].'</td><td class="expand">'.htmlspecialchars($row[3]).'</td></tr>';
	}
echo '</table>';
}
else {
	?>
	<p>Geen werkzaamheden.</p>
	<?php
}
?>
