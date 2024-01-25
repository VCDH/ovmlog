<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2016-2021, 2023
*/

function display_planned_tablerow($row, $date_short = FALSE) {
	//input is array with row data in order:
	//`id`, `datetime_start`, `datetime_end`, `road`, `location`, `scenario`, `type`, `name`, `spare`, `username_assigned`
	if (($row[8] == '1') || in_array($row[5], array('nee', 'reserve'))) {
		echo '<tr class="low">';
	}
	elseif (in_array($row[5], array('handmatig'))) {
		echo '<tr class="manual">';
	}
	elseif (in_array($row[5], array('monitoren'))) {
		echo '<tr class="monitor">';
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
        if ($date_short == TRUE) {
            echo date('d-m-Y H:i',$row[1]);
        }
        else { //long date format
		    echo ((date('Y')==date('Y',$row[1]))?(strtolower(strftime("%a %e %b %H:%M", $row[1]))):(strtolower(strftime("%a %e %b %G %H:%M", $row[1]))));
        }
	}
	echo '</td><td>';
	if (empty($row[2])) {
		echo '';
	}
	else {
		$row[2] = strtotime($row[2]);
        if ($date_short == TRUE) {
            echo date('d-m-Y H:i',$row[2]);
        }
        else { //long date format
		    echo ((date('Y')==date('Y',$row[2]))?(strtolower(strftime("%a %e %b %H:%M", $row[2]))):(strtolower(strftime("%a %e %b %G %H:%M", $row[2]))));
        }
	}
	echo '</td><td class="expand"><a href="?p=p_view&amp;id='.$row[0].'">';
	if (!empty(trim($row[7]))) echo htmlspecialchars($row[7], ENT_SUBSTITUTE);
	elseif (empty(trim($row[3])) && empty(trim($row[4]))) echo '(leeg)';
	else echo htmlspecialchars($row[3].' - '.$row[4], ENT_SUBSTITUTE);
	echo '</a></td><td class="user">';
	echo ((!empty($row[9])) ? htmlspecialchars($row[9], ENT_SUBSTITUTE) : ''); //toegewezen aan
	echo '</td><td>';
	echo (($row[8] == '1') ? 'ja' : ''); //reserve
	echo '</td><td>';
	echo htmlspecialchars($row[5], ENT_SUBSTITUTE);
	echo '</td></tr>';
}

?>