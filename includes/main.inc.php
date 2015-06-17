<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/
setlocale(LC_ALL, 'Dutch_Netherlands', 'Dutch', 'nl_NL', 'nl', 'nl_NL.ISO8859-1', 'nld_NLD', 'nl_NL.utf8');
?>
<div class="noprint">
<p class="noprint"><a href="?">Vernieuwen</a></p>
<h1>Op dit moment</h1>
<h2>Incidenten</h2>
<p class="noprint"><a href="?p=i">nieuw</a> | <a href="?p=i_hist">historie</a></p>

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
		echo '<tr><td><a href="?p=i&amp;id='.$row[0].'">'.((date('Y')==date('Y',strtotime($row[1])))?(strtolower(strftime("%a %e %b", strtotime($row[1])))):(strtolower(strftime("%a %e %b %G", strtotime($row[1]))))).'</a></td><td>&nbsp;</td><td><a href="?p=i&amp;id='.$row[0].'">'.htmlspecialchars($row[2].' - '.$row[3]).'</a></td></tr>';
		$qry2 = "SELECT `time`, `description`
		FROM `".$sql['database']."`.`".$sql['table_id']."`
		WHERE `parent_id` = ".$row[0]."
		ORDER BY `time`";
		$res2 = mysqli_query($sql['link'], $qry2);
		if (mysqli_num_rows($res2)) {
			while ($row2 = mysqli_fetch_row($res2)) {
				//nog iets toevoegen van string afbreken na bepaalde lengte
				echo '<tr class="sub"><td>&nbsp;</td><td>'.date('H:i', strtotime($row2[0])).'</td><td class="expand">'.htmlspecialchars(str_replace('<br />', ' ', $row2[1]), NULL, 'ISO-8859-15').'</td></tr>';
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
</div>
<h2>Werkzaamheden</h2>
<p class="noprint"><a href="?p=w">nieuw</a> | <a href="?p=w_hist">historie</a></p>

<?php
$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `road`, `location`, `scenario` 
	FROM `".$sql['database']."`.`".$sql['table_w']."`
	WHERE `datetime_end` > NOW()
	AND `datetime_start` < NOW()
	ORDER BY `datetime_end`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<h3>Nu</h3>';
	echo '<table class="grid">';
	echo '<tr><th>start</th><th>eind</th><th>locatie</th><th>scn</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr'.(($row[5]=='nee')?' class="low"':'').'><td>'.
        ((date('Y')==date('Y',strtotime($row[1])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[1])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[1]))))).
        '</td><td>'.
        ((date('Y')==date('Y',strtotime($row[2])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[2])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[2]))))).
        '</td><td class="expand"><a href="?p=w_view&amp;id='.$row[0].'">';
		if (empty($row[3]) && empty($row[4])) echo '(leeg)';
		else echo htmlspecialchars($row[3].' - '.$row[4]);
		echo '</a></td><td>';
		echo htmlspecialchars($row[5]);
		echo '</td></tr>';
	}
	echo '</table>';
}

$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `road`, `location`, `scenario` 
	FROM `".$sql['database']."`.`".$sql['table_w']."`
	WHERE `datetime_start` > NOW()
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<h3>Gepland</h3>';
	echo '<table class="grid">';
	echo '<tr><th>start</th><th>eind</th><th>locatie</th><th>scn</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr'.((strtotime($row[1])<time()+604800)?' class="upcoming"':'').'><td>'.
        ((date('Y')==date('Y',strtotime($row[1])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[1])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[1]))))).
        '</td><td>'.
        ((date('Y')==date('Y',strtotime($row[2])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[2])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[2]))))).
        '</td><td class="expand"><a href="?p=w_view&amp;id='.$row[0].'">';
		if (empty($row[3]) && empty($row[4])) echo '(leeg)';
		else echo htmlspecialchars($row[3].' - '.$row[4]);
		echo '</a></td><td>';
		echo htmlspecialchars($row[5]);
		echo '</td></tr>';
	}
	echo '</table>';
}
?>

<hr />
<h2>Evenementen</h2>
<p class="noprint"><a href="?p=e">nieuw</a> | <a href="?p=e_hist">historie</a></p>

<?php
$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `name`, `scenario` 
	FROM `".$sql['database']."`.`".$sql['table_e']."`
	WHERE `datetime_end` > NOW()
	AND `datetime_start` < NOW()
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<h3>Nu</h3>';
	echo '<table class="grid">';
	echo '<tr><th>start</th><th>eind</th><th>omschrijving</th><th>scn</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr'.(($row[5]=='nee')?' class="low"':'').'><td>'.
        ((date('Y')==date('Y',strtotime($row[1])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[1])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[1]))))).
        '</td><td>'.
        ((date('Y')==date('Y',strtotime($row[2])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[2])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[2]))))).
        '</td><td class="expand"><a href="?p=e_view&amp;id='.$row[0].'">';
		if (empty($row[3])) echo '(leeg)';
		else echo htmlspecialchars($row[3]);
		echo '</a></td><td>';
		echo htmlspecialchars($row[4]);
		echo '</td></tr>';
	}
	echo '</table>';
}

$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `name`, `scenario` 
	FROM `".$sql['database']."`.`".$sql['table_e']."`
	WHERE `datetime_start` > NOW()
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<h3>Gepland</h3>';
	echo '<table class="grid">';
	echo '<tr><th>start</th><th>eind</th><th>omschrijving</th><th>scn</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr'.((strtotime($row[1])<time()+604800)?' class="upcoming"':'').'><td>'.
        ((date('Y')==date('Y',strtotime($row[1])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[1])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[1]))))).
        '</td><td>'.
        ((date('Y')==date('Y',strtotime($row[2])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[2])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[2]))))).
        '</td><td class="expand"><a href="?p=e_view&amp;id='.$row[0].'">';
		if (empty($row[3])) echo '(leeg)';
		else echo htmlspecialchars($row[3]);
		echo '</a></td><td>';
		echo htmlspecialchars($row[4]);
		echo '</td></tr>';
	}
	echo '</table>';
}
?>
