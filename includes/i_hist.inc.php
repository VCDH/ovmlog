<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/
?>

<h2>Incidenten</h2>
<p><a href="?">&laquo; terug</a> | <a href="?p=i_analyse">analyse</a></p>

<?php
$qry = "SELECT `id`, `date`, `road`, `location`, `scenario`, `review`  
	FROM `".$sql['database']."`.`".$sql['table_i']."`
	ORDER BY `date` DESC";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th>datum</th><th>tijd</th><th>locatie/omschrijving</th><th>scenario</th><th>evaluatie</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		$qry2 = "SELECT `time`
		FROM `".$sql['database']."`.`".$sql['table_id']."`
		WHERE `parent_id` = ".$row[0]."
		ORDER BY `id`
		LIMIT 1";
		$res2 = mysqli_query($sql['link'], $qry2);
		if (mysqli_num_rows($res2)) {
			$row2 = mysqli_fetch_row($res2);
		}
		echo '<tr><td>'.date('d-m-Y', strtotime($row[1])).'</td><td>'.date('H:i', strtotime($row2[0])).'</td><td class="expand"><a href="?p=i_view&amp;id='.$row[0].'">'.htmlspecialchars($row[2].' - '.$row[3]).'</a></td><td>'.htmlspecialchars($row[4]).'</td><td>'.(($row[5] == '1') ? 'Ja' : '').'</td></tr>';
	}
	echo '</table>';
}
else {
	?>
	<p>Geen incidenten.</p>
	<?php
}
?>