<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

$msg_type = substr($msg, 0, 1);

//error messages
if ($msg_type == 'e') {
	echo '<p class="error">';
	switch ( (int) substr($msg, 1, 3)) {
		case 1: echo 'Kan niet opslaan.'; break;
		case 2: echo 'Kan omschrijving niet opslaan.'; break;
	}
	echo '</p>';
}
//success messages
elseif ($msg_type == 's') {
	echo '<p class="success">';
	switch ( (int) substr($msg, 1, 3)) {
		case 1: echo 'Opgeslagen.'; break;
	}
	echo '</p>';
}
?>