<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/
?>

<h2>Incidenten</h2>
<p><a href="?">&laquo; terug</a> | <a href="?p=i_analyse">analyse</a></p>

<?php
$qry = "SELECT `id`, `date` 
	FROM `".$sql['database']."`.`".$sql['table_i']."`
	ORDER BY `date` DESC";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th>datum</th><th>tijd</th><th>omschrijving</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr><td><a href="?p=i_view&amp;id='.$row[0].'">'.$row[1].'</a></td>';
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
	<p>Geen incidenten.</p>
	<?php
}
?>