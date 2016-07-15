<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2016
*/
setlocale(LC_ALL, 'Dutch_Netherlands', 'Dutch', 'nl_NL', 'nl', 'nl_NL.ISO8859-1', 'nld_NLD', 'nl_NL.utf8');
?>
<div class="noprint">
<h1>Incidenten</h1>
<p><a href="?p=i">nieuw</a> | <a href="?p=i_hist">historie</a> | <a href="?">vernieuwen</a></p>

<?php
$qry = "SELECT `id`, `date`, `road`, `location` 
	FROM `".$sql['database']."`.`".$sql['table_i']."`
	WHERE `open` = 1
	ORDER BY `date` DESC";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
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

</div>

<hr />
<h1>Actuele werkzaamheden en evenementen</h1>

<?php
$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `road`, `location`, `scenario`, `type`, `name` 
	FROM `".$sql['database']."`.`".$sql['table_p']."`
	WHERE `datetime_end` > NOW()
	AND `datetime_start` < NOW()
	ORDER BY `datetime_end`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th></th><th>start</th><th>eind</th><th>locatie/naam</th><th>scn</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr'.(($row[5]=='nee')?' class="low"':'').'>';
        echo '<td><img src="'.(($row[6] == 'w') ? 'werk' : 'evenement').'.png" width="16" height="16" alt="'.(($row[6] == 'w') ? 'werk' : 'evenement').'" title="'.(($row[6] == 'w') ? 'werk' : 'evenement').'" /></td>';
        echo '<td>'.
        ((date('Y')==date('Y',strtotime($row[1])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[1])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[1]))))).
        '</td><td>'.
        ((date('Y')==date('Y',strtotime($row[2])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[2])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[2]))))).
        '</td><td class="expand"><a href="?p=p_view&amp;id='.$row[0].'">';
		if (!empty($row[7])) echo htmlspecialchars($row[7]);
        elseif (empty($row[3]) && empty($row[4])) echo '(leeg)';
		else echo htmlspecialchars($row[3].' - '.$row[4]);
		echo '</a></td><td>';
		echo htmlspecialchars($row[5]);
		echo '</td></tr>';
	}
	echo '</table>';
}
else {
    echo '<p>Er zijn geen werkzaamheden of evenementen.</p>';
}
?>

<hr />
<h1>Geplande werkzaamheden en evenementen</h1>
<p class="noprint"><a href="?p=p">nieuw</a> | <a href="?p=p_hist">historie</a></p>

<?php
$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `road`, `location`, `scenario`, `type`, `name`  
	FROM `".$sql['database']."`.`".$sql['table_p']."`
	WHERE `datetime_start` > NOW()
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th></th><th>start</th><th>eind</th><th>locatie</th><th>scn</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr'.((strtotime($row[1])<time()+604800)?' class="upcoming"':'').'>';
        echo '<td><img src="'.(($row[6] == 'w') ? 'werk' : 'evenement').'.png" width="16" height="16" alt="'.(($row[6] == 'w') ? 'werk' : 'evenement').'" title="'.(($row[6] == 'w') ? 'werk' : 'evenement').'" /></td>';
        echo '<td>'.
        ((date('Y')==date('Y',strtotime($row[1])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[1])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[1]))))).
        '</td><td>'.
        ((date('Y')==date('Y',strtotime($row[2])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[2])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[2]))))).
        '</td><td class="expand"><a href="?p=p_view&amp;id='.$row[0].'">';
		if (!empty($row[7])) echo htmlspecialchars($row[7]);
        elseif (empty($row[3]) && empty($row[4])) echo '(leeg)';
		else echo htmlspecialchars($row[3].' - '.$row[4]);
		echo '</a></td><td>';
		echo htmlspecialchars($row[5]);
		echo '</td></tr>';
	}
	echo '</table>';
}
else {
    echo '<p>Er zijn geen geplande werkzaamheden of evenementen.</p>';
}
?>

<p class="noprint"><a href="?p=kce">KCE printweergave genereren</a></p>
