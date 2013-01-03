<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/
?>

<h2>Werkzaamheden - historie</h2>
<p><a href="?">&laquo; terug</a> | <a href="?p=w_analyse">analyse</a></p>

<?php
$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `name` 
	FROM `".$sql['database']."`.`".$sql['table_e']."`
	ORDER BY `datetime_start` DESC";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th>start</th><th>eind</th><th>omschrijving</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr><td>'.date('d-m-Y H:i', strtotime($row[1])).'</td><td>'.date('d-m-Y H:i', strtotime($row[2])).'</td><td class="expand"><a href="?p=e_view&amp;id='.$row[0].'">';
		if (empty($row[3])) echo '(leeg)';
		else echo htmlspecialchars($row[3]);
		echo '</a></td></tr>';
	}
	echo '</table>';
}
else {
	?>
	<p>Geen werkzaamheden.</p>
	<?php
}
?>