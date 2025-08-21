<?php
/*
	ovmlog - logtool voor operationeel verkeersmanagement
	Copyright (C) 2013, 2016-2021, 2025 Gemeente Den Haag, Netherlands
    Developed by Jasper Vries
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/

function int_date_format($format, $time) {
	//default format
	if (empty($format)) {
		$format = 'EEEEEE d MMM HH:mm';
	}
	//date formatter
	$datefmt = datefmt_create(
		'nl_NL',
		IntlDateFormatter::FULL,
		IntlDateFormatter::FULL,
		'Europe/Amsterdam',
		IntlDateFormatter::GREGORIAN,
		$format
	);
	//convert to timestamp
	if (!is_numeric($time)) {
		$time =  strtotime($time);
	}
	//output
	return strtolower(str_replace('.', '', datefmt_format($datefmt, $time)));
}

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
		    echo ((date('Y')==date('Y',$row[1]))?(int_date_format(NULL, $row[1])):(int_date_format('EEEEEE d MMM y HH:mm', $row[1])));
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
		    echo ((date('Y')==date('Y',$row[2]))?(int_date_format(NULL, $row[2])):(int_date_format('EEEEEE d MMM y HH:mm', $row[2])));
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