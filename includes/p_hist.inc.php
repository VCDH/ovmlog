<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2016
*/
?>

<h2>Werkzaamheden en evenementen - historie</h2>
<p><a href="?">&laquo; terug</a></p>

<?php
$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `road`, `location`, `type`, `name`, `spare` 
	FROM `".$sql['database']."`.`".$sql['table_p']."`
	ORDER BY `datetime_start` DESC";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th></th><th>start</th><th>eind</th><th>locatie</th><th title="reserve">res</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr>';
        echo '<td><img src="'.(($row[5] == 'w') ? 'werk' : 'evenement').'.png" width="16" height="16" alt="'.(($row[5] == 'w') ? 'werk' : 'evenement').'" title="'.(($row[5] == 'w') ? 'werk' : 'evenement').'" /></td>';
        echo '<td>'.date('d-m-Y H:i', strtotime($row[1])).'</td><td>'.date('d-m-Y H:i', strtotime($row[2])).'</td><td class="expand"><a href="?p=p_view&amp;id='.$row[0].'">';
		if (!empty($row[6])) echo htmlspecialchars($row[6]);
        elseif (empty($row[3]) && empty($row[4])) echo '(leeg)';
		else echo htmlspecialchars($row[3].' - '.$row[4]);
		echo '</a></td><td>';
		echo (($row[7] == '1') ? 'ja' : '');
		echo '</td></tr>';
	}
	echo '</table>';
}
else {
	?>
	<p>Geen werkzaamheden of evenementen.</p>
	<?php
}
?>