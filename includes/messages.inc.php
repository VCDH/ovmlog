<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

$msg_type = substr($_GET['msg'], 0, 1);

//error messages
if ($msg_type == 'e') {
	echo '<p class="error">';
	switch ( (int) substr($_GET['msg'], 1, 3)) {
		case 1: echo 'Kan niet opslaan.'; break;
		case 2: echo 'Kan omschrijving niet opslaan.'; break;
		case 3: echo 'Gebruikersnaam/wachtwoord combinatie komt niet overeen.'; break;
		case 4: echo 'Kan cookie niet zetten. Zorg ervoor dat je browser cookies accepteert.'; break;
		case 5: echo 'Niet afgemeld. Kan cookie niet zetten. Zorg ervoor dat je browser cookies accepteert.'; break;
		case 6: echo 'Kan niet verwijderen. Item bestaat niet (meer).'; break;
		case 7: echo 'Veld kan niet leeg zijn.'; break;
	}
	echo '</p>';
}
//success messages
elseif ($msg_type == 's') {
	echo '<p class="success">';
	switch ( (int) substr($_GET['msg'], 1, 3)) {
		case 1: echo 'Opgeslagen.'; break;
		case 2: echo 'Afgemeld.'; break;
		case 3: echo 'Verwijderd.'; break;
	}
	echo '</p>';
}
?>