<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2016, 2021
*/
setlocale(LC_ALL, 'Dutch_Netherlands', 'Dutch', 'nl_NL', 'nl', 'nl_NL.ISO8859-1', 'nld_NLD', 'nl_NL.utf8');
?>

<script>
$(function() {
	//timepicker voor daglog
	$( "#time" ).timepicker();
});
</script>

<div class="noprint">
<h1>Daglogging</h1>
<p><a href="?p=d_view">bekijken</a></p>
<?php
//laatste entry
$qry = "SELECT * FROM (
		SELECT `datetime`, `description`, `".$sql['table_users']."`.`username` AS `username`, `".$sql['table_d']."`.`id` AS `id`, `sticky`
		FROM `".$sql['database']."`.`".$sql['table_d']."`
		LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."`
		ON `".$sql['table_users']."`.`id` = `".$sql['table_d']."`.`user_id_edit`
		WHERE `sticky` = FALSE
		ORDER BY `".$sql['table_d']."`.`id` DESC
		LIMIT 5
	) AS `t1`
	UNION
	SELECT * FROM (
		SELECT `datetime`, `description`, `".$sql['table_users']."`.`username` AS `username`, `".$sql['table_d']."`.`id` AS `id`, `sticky`
		FROM `".$sql['database']."`.`".$sql['table_d']."`
		LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."`
		ON `".$sql['table_users']."`.`id` = `".$sql['table_d']."`.`user_id_create`
		WHERE `sticky` = TRUE
	) AS `t2`
	ORDER BY `datetime` ASC, `id` ASC";
$res = mysqli_query($sql['link'], $qry);
echo mysqli_error($sql['link']);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr><td>';
		echo strtolower(strftime("%a %e %b %H:%M", strtotime($row[0])));
		echo '</td><td class="expand">';
		echo htmlspecialchars($row[1], ENT_SUBSTITUTE);
		//sticky
		if ($row[4] == 1) {
			echo '<a href="?s=d&amp;&amp;do=unsticky&amp;id=' . $row[3] . '" title="Losmaken"><span class="ui-icon ui-icon-pin-s"></span>';
		}
		echo '</td><td>';
		echo ((!empty($row[2])) ? htmlspecialchars($row[2], ENT_SUBSTITUTE) : '');
		echo '</td></tr>';
	}
	echo '</table>';
}
?>
<form method="post" action="?s=d">
<label for="entry">Nieuwe entry:</label> <input type="text" name="time" id="time" class="time"> <input type="text" name="entry" id="entry" class="l"> <input type="checkbox" name="sticky" value="true" title="Vastzetten"> <input type="submit" value="Toevoegen">
</form>
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
		echo '<tr><td><a href="?p=i&amp;id='.$row[0].'">'.((date('Y')==date('Y',strtotime($row[1])))?(strtolower(strftime("%a %e %b", strtotime($row[1])))):(strtolower(strftime("%a %e %b %G", strtotime($row[1]))))).'</a></td><td>&nbsp;</td><td><a href="?p=i&amp;id='.$row[0].'">'.htmlspecialchars($row[2].' - '.$row[3], ENT_SUBSTITUTE).'</a></td></tr>';
		$qry2 = "SELECT `time`, `description`
		FROM `".$sql['database']."`.`".$sql['table_id']."`
		WHERE `parent_id` = ".$row[0]."
		ORDER BY `time`";
		$res2 = mysqli_query($sql['link'], $qry2);
		if (mysqli_num_rows($res2)) {
			while ($row2 = mysqli_fetch_row($res2)) {
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
$qry = "SELECT `".$sql['table_p']."`.`id`, `datetime_start`, `datetime_end`, `road`, `location`, `scenario`, `type`, `name`, `spare`, `".$sql['table_users']."`.`username` AS `assigned`
	FROM `".$sql['database']."`.`".$sql['table_p']."`
	LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."`
	ON `".$sql['table_users']."`.`id` = `".$sql['table_p']."`.`user_id_assigned`
	WHERE `datetime_end` > NOW()
	AND `datetime_start` < NOW()
	ORDER BY `datetime_end`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th></th><th>start</th><th>eind</th><th>locatie/naam</th><th title="toegewezen aan">toeg.</th><th title="reserve">res</th><th title="scenario">scn</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		if (in_array($row[5], array('nee', 'reserve'))) {
            echo '<tr class="low">';
        }
        elseif (in_array($row[5], array('handmatig'))) {
            echo '<tr class="manual">';
        }
        elseif (!in_array($row[5], array('geactiveerd', 'handmatig', 'DVM-Exchange', 'PZH-Deelscenario'))) {
            echo '<tr class="attention">';
        }
        else {
            echo '<tr>';
        }
        echo '<td><img src="'.(($row[6] == 'w') ? 'werk' : 'evenement').'.png" width="16" height="16" alt="'.(($row[6] == 'w') ? 'werk' : 'evenement').'" title="'.(($row[6] == 'w') ? 'werk' : 'evenement').'" /></td>';
        echo '<td>'.
        ((date('Y')==date('Y',strtotime($row[1])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[1])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[1]))))).
        '</td><td>'.
        ((date('Y')==date('Y',strtotime($row[2])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[2])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[2]))))).
        '</td><td class="expand"><a href="?p=p_view&amp;id='.$row[0].'">';
		if (!empty($row[7])) echo htmlspecialchars($row[7], ENT_SUBSTITUTE);
        elseif (empty($row[3]) && empty($row[4])) echo '(leeg)';
		else echo htmlspecialchars($row[3].' - '.$row[4], ENT_SUBSTITUTE);
		echo '</a></td><td>';
		echo ((!empty($row[9])) ? htmlspecialchars($row[9], ENT_SUBSTITUTE) : ''); //toegewezen aan
		echo '</td><td>';
		echo (($row[8] == '1') ? 'ja' : ''); //reserve
		echo '</td><td>';
		echo htmlspecialchars($row[5], ENT_SUBSTITUTE);
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

function display_planned_tablerow($row) {
	//input is array with row data in order:
	//`id`, `datetime_start`, `datetime_end`, `road`, `location`, `scenario`, `type`, `name`, `spare`, `username_assigned`
	if (in_array($row[5], array('nee', 'reserve'))) {
		echo '<tr class="low">';
	}
	elseif ((strtotime($row[1])<time()+604800) && !empty($row[1])) {
		if (!in_array($row[5], array('geactiveerd', 'handmatig', 'DVM-Exchange', 'PZH-Deelscenario'))) {
			echo '<tr class="attention">';
		}
		else {
			echo '<tr class="upcoming">';
		}
	}
	else {
		echo '<tr>';
	}        
	echo '<td><img src="'.(($row[6] == 'w') ? 'werk' : 'evenement').'.png" width="16" height="16" alt="'.(($row[6] == 'w') ? 'werk' : 'evenement').'" title="'.(($row[6] == 'w') ? 'werk' : 'evenement').'" /></td>';
	echo '<td>';
	if (empty($row[1])) {
		echo '(onbekend)';
	}
	else {
		$row[1] = strtotime($row[1]);
		echo ((date('Y')==date('Y',$row[1]))?(strtolower(strftime("%a %e %b %H:%M", $row[1]))):(strtolower(strftime("%a %e %b %G %H:%M", $row[1]))));
	}
	echo '</td><td>';
	if (empty($row[2])) {
		echo '';
	}
	else {
		$row[2] = strtotime($row[2]);
		echo ((date('Y')==date('Y',$row[2]))?(strtolower(strftime("%a %e %b %H:%M", $row[2]))):(strtolower(strftime("%a %e %b %G %H:%M", $row[2]))));
	}
	echo '</td><td class="expand"><a href="?p=p_view&amp;id='.$row[0].'">';
	if (!empty($row[7])) echo htmlspecialchars($row[7], ENT_SUBSTITUTE);
	elseif (empty($row[3]) && empty($row[4])) echo '(leeg)';
	else echo htmlspecialchars($row[3].' - '.$row[4], ENT_SUBSTITUTE);
	echo '</a></td><td>';
	echo ((!empty($row[9])) ? htmlspecialchars($row[9], ENT_SUBSTITUTE) : ''); //toegewezen aan
	echo '</td><td>';
	echo (($row[8] == '1') ? 'ja' : ''); //reserve
	echo '</td><td>';
	echo htmlspecialchars($row[5], ENT_SUBSTITUTE);
	echo '</td></tr>';
}


echo '<table class="grid">';
echo '<tr><th></th><th>start</th><th>eind</th><th>locatie</th><th title="toegewezen aan">toeg.</th><th title="reserve">res</th><th title="scenario">scn</th></tr>';
//planned with date
$qry = "SELECT `".$sql['table_p']."`.`id`, `datetime_start`, `datetime_end`, `road`, `location`, `scenario`, `type`, `name`, `spare`, `".$sql['table_users']."`.`username` AS `assigned`   
	FROM `".$sql['database']."`.`".$sql['table_p']."`
	LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."`
	ON `".$sql['table_users']."`.`id` = `".$sql['table_p']."`.`user_id_assigned`
	WHERE `datetime_start` > NOW()
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
while ($row = mysqli_fetch_row($res)) {
	display_planned_tablerow($row);
}
//planned without date at the end
$qry = "SELECT `".$sql['table_p']."`.`id`, `datetime_start`, `datetime_end`, `road`, `location`, `scenario`, `type`, `name`, `spare`, `".$sql['table_users']."`.`username` AS `assigned`   
	FROM `".$sql['database']."`.`".$sql['table_p']."`
	LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."`
	ON `".$sql['table_users']."`.`id` = `".$sql['table_p']."`.`user_id_assigned`
	WHERE DATE(`datetime_start`) = '1970-01-01'
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
while ($row = mysqli_fetch_row($res)) {
	$row[1] = '';
	$row[2] = '';
	display_planned_tablerow($row);
}
echo '</table>';
?>

<p class="noprint"><a href="?p=kce">KCE printweergave genereren</a></p>
