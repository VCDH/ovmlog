<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/
?>

<h1>Op dit moment</h1>
<h2>Incidenten</h2>
<p><a href="?p=i">nieuw</a> | <a href="?p=i_hist">historie</a></p>

<?php
$qry = "SELECT `id`, `date`, `road`, `location` 
	FROM `".$sql['database']."`.`".$sql['table_i']."`
	WHERE `open` = 1
	ORDER BY `date` DESC";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<h3>Nu</h3>';
	echo '<table class="grid">';
	echo '<tr><th>datum</th><th>tijd</th><th>locatie/omschrijving</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr><td><a href="?p=i&amp;id='.$row[0].'">'.date('d-m-Y', strtotime($row[1])).'</a></td><td>&nbsp;</td><td><a href="?p=i&amp;id='.$row[0].'">'.htmlspecialchars($row[2].' - '.$row[3]).'</a></td></tr>';
		$qry2 = "SELECT `time`, `description`
		FROM `".$sql['database']."`.`".$sql['table_id']."`
		WHERE `parent_id` = ".$row[0]."
		ORDER BY `time`";
		$res2 = mysqli_query($sql['link'], $qry2);
		if (mysqli_num_rows($res2)) {
			while ($row2 = mysqli_fetch_row($res2)) {
				//nog iets toevoegen van string afbreken na bepaalde lengte
				echo '<tr class="sub"><td>&nbsp;</td><td>'.date('H:i', strtotime($row2[0])).'</td><td class="expand">'.htmlspecialchars(str_replace('<br />', ' ', $row2[1])).'</td></tr>';
			}
		}
	}
	echo '</table>';
}
else {
	?>
	<p>Geen openstaande incidenten.</p>
	<?php
}
?>

<hr />
<h2>Werkzaamheden</h2>
<p><a href="?p=w">nieuw</a> | <a href="?p=w_hist">historie</a></p>

<?php
$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `road`, `location` 
	FROM `".$sql['database']."`.`".$sql['table_w']."`
	WHERE `datetime_end` > NOW()
	AND `datetime_start` < NOW()
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<h3>Nu</h3>';
	echo '<table class="grid">';
	echo '<tr><th>start</th><th>eind</th><th>locatie</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr><td>'.date('d-m-Y H:i', strtotime($row[1])).'</td><td>'.date('d-m-Y H:i', strtotime($row[2])).'</td><td class="expand"><a href="?p=w_view&amp;id='.$row[0].'">';
		if (empty($row[3]) && empty($row[4])) echo '(leeg)';
		else echo htmlspecialchars($row[3].' - '.$row[4]);
		echo '</a></td></tr>';
	}
	echo '</table>';
}

$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `road`, `location` 
	FROM `".$sql['database']."`.`".$sql['table_w']."`
	WHERE `datetime_start` > NOW()
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<h3>Gepland</h3>';
	echo '<table class="grid">';
	echo '<tr><th>start</th><th>eind</th><th>locatie</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr><td>'.date('d-m-Y H:i', strtotime($row[1])).'</td><td>'.date('d-m-Y H:i', strtotime($row[2])).'</td><td class="expand"><a href="?p=w_view&amp;id='.$row[0].'">';
		if (empty($row[3]) && empty($row[4])) echo '(leeg)';
		else echo htmlspecialchars($row[3].' - '.$row[4]);
		echo '</a></td></tr>';
	}
	echo '</table>';
}
?>

<hr />
<h2>Evenementen</h2>
<p><a href="?p=e">nieuw</a> | <a href="?p=e_hist">historie</a></p>

<?php
$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `name` 
	FROM `".$sql['database']."`.`".$sql['table_e']."`
	WHERE `datetime_end` > NOW()
	AND `datetime_start` < NOW()
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<h3>Nu</h3>';
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

$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `name` 
	FROM `".$sql['database']."`.`".$sql['table_e']."`
	WHERE `datetime_start` > NOW()
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<h3>Gepland</h3>';
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
?>
