<?php
/*
	ovmlog - logtool voor operationeel verkeersmanagement
	Copyright (C) 2013, 2025 Gemeente Den Haag, Netherlands
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
$msg_type = NULL;
if (array_key_exists('msg', $_GET))
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