<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2022
*/

//controleer of mag bekijken
if (permissioncheck('bekijk_inc') !== true) {
	echo 'niet toegestaan';
	exit;
}

$max_autocomplete = 50;

//decide edit or add
if (!empty($_GET['id'])) $id = $_GET['id'];
elseif (!empty($_POST['id'])) $id = $_POST['id'];


if (!empty($id)) {
	$qry = "SELECT * 
	FROM `".$sql['database']."`.`".$sql['table_i']."`
	WHERE `id` = '".mysqli_real_escape_string($sql['link'], $id)."'
	LIMIT 1";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res)) {
		$edit = TRUE;
	}
}

if ($edit === TRUE) {
	//load values
	$title = 'Incident bewerken';
	$data = mysqli_fetch_assoc($res);
	
	$date = date('d-m-Y', strtotime($data['date']));
	$road = htmlspecialchars($data['road'], ENT_SUBSTITUTE);
	$location = htmlspecialchars($data['location'], ENT_SUBSTITUTE);
	$scenario = htmlspecialchars($data['scenario'], ENT_SUBSTITUTE);
	$open = $data['open'];
    $review = $data['review'];
    $regelaanpak = $data['regelaanpak'];
	
	$qry = "SELECT * 
	FROM `".$sql['database']."`.`".$sql['table_id']."`
	WHERE `parent_id` = '".mysqli_real_escape_string($sql['link'], $id)."'
	ORDER BY `time`";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res)) {
		while ($data = mysqli_fetch_assoc($res)) {
			$content[] = array(	'id' => $data['id'], 
								'time' => date('H:i', strtotime($data['time'])), 
								'description' => htmlspecialchars($data['description'], NULL, 'ISO-8859-15'), 
								'contact' => htmlspecialchars($data['contact'], ENT_SUBSTITUTE));
		}
	}
	$content[] = array(	'id' => '0', 
						'time' => date('H:i'), 
						'description' => null, 
						'content' => null);
}
else {
//default values
$title = 'Incident toevoegen';
$date = date('d-m-Y');
$open = 1;
$review = 0;
$regelaanpak = 0;
$content[] = array(	'id' => '0', 
					'time' => date('H:i'), 
					'description' => null, 
					'content' => null);
}
?>

<script>
$(function() {
	$( ".date" ).datepicker();
	$( ".time" ).timepicker();
	
	$( "#scenario" ).autocomplete({
		source: [<?php
			$autocomplete = array();
			$qry = "SELECT DISTINCT `scenario` 
			FROM `".$sql['database']."`.`".$sql['table_i']."`";
			$res = mysqli_query($sql['link'], $qry);
			while ($data = mysqli_fetch_row($res)) {
				if (!empty($data[0])) $autocomplete[] = '"'.addslashes(htmlspecialchars($data[0], ENT_SUBSTITUTE)).'"';
			}
			echo implode(',', $autocomplete);
		?>],
		delay: 0,
		minLength: 2
	});
	$( "#road" ).autocomplete({
		source: [<?php
			$autocomplete = array();
			$qry = "SELECT DISTINCT `road` 
			FROM `".$sql['database']."`.`".$sql['table_i']."`";
			$res = mysqli_query($sql['link'], $qry);
			while ($data = mysqli_fetch_row($res)) {
				if (!empty($data[0])) $autocomplete[] = '"'.addslashes(htmlspecialchars($data[0], ENT_SUBSTITUTE)).'"';
			}
			echo implode(',', $autocomplete);
		?>],
		delay: 0,
		minLength: 2
	});
	$( ".contact" ).autocomplete({
		source: [<?php
			$autocomplete = array();
			$qry = "SELECT `contact` 
			FROM `".$sql['database']."`.`".$sql['table_id']."`
			GROUP BY `contact`
			ORDER BY count(*) DESC";
			$res = mysqli_query($sql['link'], $qry);
			while ($data = mysqli_fetch_row($res)) {
				if (!empty($data[0])) $autocomplete[] = '"'.addslashes(htmlspecialchars($data[0], ENT_SUBSTITUTE)).'"';
			}
			echo implode(',', $autocomplete);
		?>],
		delay: 0,
		minLength: 3
	});
	
	$(document).ready( function () {
 		$('#formulier').submit( function (event) {
			if ($('#scenario').val() == 'geen') {
				alert('Er bestaat geen scenario met de naam "geen". Laat het veld leeg als er geen scenario is ingezet.');
				event.preventDefault();
			}
		});
		if ($('#scenario').val() == 'maatwerk') {
 			$('#maatwerk').attr('checked', 'checked');
 			$('#scenario').attr('readonly', 'readonly');
 		}
 	});
 	$('#maatwerk').change( function (){
 		if ($('#maatwerk').is(':checked') == true) {
 			$('#scenario').val('maatwerk');
 			$('#scenario').attr('readonly', 'readonly');
 		}
 		else {
 			$('#scenario').val('');
 			$('#scenario').removeAttr('readonly');
 		}
 	});
 	$('#scenario').change( function (){
 		if ($('#scenario').val() == 'maatwerk') {
 			$('#maatwerk').attr('checked', 'checked');
 			$('#scenario').attr('readonly', 'readonly');
 		}
 	});
});
</script>

<h1><?php echo $title; ?></h1>

<form id="formulier" action="?s=i" method="post">
<input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_SUBSTITUTE) ?>" />

<table>
<tr>
	<td><label for="date">datum:</label></td>
	<td><input tabindex="1" class="date s" name="date" id="date" type="text" value="<?php echo $date; ?>" /></td>
    <td><label for="road">wegnr:</label></td>
	<td><input tabindex="2" class="s" name="road" id="road" type="text" value="<?php echo $road; ?>" /></td>
</tr>
<tr>
	<td><label for="scenario">scenario:</label></td>
	<td><input tabindex="4" class="m" name="scenario" id="scenario" type="text" value="<?php echo $scenario; ?>" /> </td>
	<td><label for="location">locatie:</label></td>
	<td><input tabindex="3" class="m" name="location" id="location" type="text" value="<?php echo $location; ?>" /></td>
</tr>
<tr>
	<td colspan="4"><input type="checkbox" name="maatwerk" id="maatwerk" value="true" /> <label for="maatwerk">maatwerk</label> <input type="checkbox" name="review" id="review" value="true"<?php if ($review == 1) echo ' checked="checked"'; ?> /> <label for="review">evaluatie</label> <input type="checkbox" name="regelaanpak" id="regelaanpak" value="true"<?php if ($regelaanpak == 1) echo ' checked="checked"'; ?> /> <label for="regelaanpak">regelaanpak</label></td>
</tr>
</table>

<table>
<tr>
	<td>&nbsp;</td>
	<td><label for="time0">tijd</label></td>
	<td><label for="description0">beschrijving</label></td>
	<td><label for="contact0">gesproken met</label></td>
</tr>

<?php
foreach ($content as $count => $values) {
	?>
	<tr>
		<td class="count"><?php echo $count+1; ?></td>
		<td><input class="time" name="time[<?php echo $values['id']; ?>]" id="time<?php echo $values['id']; ?>" type="text" value="<?php echo $values['time']; ?>" /></td>
		<td><textarea name="description[<?php echo $values['id']; ?>]" id="description<?php echo $values['id']; ?>" rows="4" cols="40"><?php echo $values['description']; ?></textarea></td>
		<td><input class="contact m" name="contact[<?php echo $values['id']; ?>]" id="contact<?php echo $values['id']; ?>" type="text" value="<?php echo $values['contact']; ?>" /></td>
	</tr>
	<?php
}
?>

<tr>
	<td>&nbsp;</td>
	<td colspan="3"><input type="checkbox" name="closed" id="closed" value="true"<?php if ($open == 0) echo ' checked="checked"';  ?> /><label for="closed">melding afsluiten</label></td>
</tr>
</table>

<p><input type="submit" name="add" value="nog een tijdstip toevoegen"> <input type="submit" name="save" value="opslaan en naar overzicht"> <a href="?">Annuleren</a></p>

</form>
