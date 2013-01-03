<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

//decide edit or add
if (!empty($_GET['id'])) {
	$qry = "SELECT * 
	FROM `".$sql['database']."`.`".$sql['table_i']."`
	WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'
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
	$road = htmlspecialchars($data['road']);
	$location = htmlspecialchars($data['location']);
	$scenario = htmlspecialchars($data['scenario']);
	$open = $data['open'];
	
	$qry = "SELECT * 
	FROM `".$sql['database']."`.`".$sql['table_id']."`
	WHERE `parent_id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'
	ORDER BY `time`";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res)) {
		while ($data = mysqli_fetch_assoc($res)) {
			$content[] = array(	'id' => $data['id'], 
								'time' => date('H:i', strtotime($data['time'])), 
								'description' => htmlspecialchars($data['description']), 
								'contact' => htmlspecialchars($data['contact']));
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
$content[] = array(	'id' => '0', 
					'time' => date('H:i'), 
					'description' => null, 
					'content' => null);
}
?>

<h1><?php echo $title; ?></h1>

<form action="?s=i" method="post">
<input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']) ?>" />

<table>
<tr>
	<td><label for="date">datum:</label></td>
	<td><input class="s" name="date" id="date" type="text" value="<?php echo $date; ?>" /></td>
	<td><label for="scenario">scenario:</label></td>
	<td><input class="m" name="scenario" id="scenario" type="text" value="<?php echo $scenario; ?>" /> (laat leeg in geval van maatwerk)</td>
</tr>
<tr>
	<td><label for="road">wegnr:</label></td>
	<td><input class="s" name="road" id="road" type="text" value="<?php echo $road; ?>" /></td>
	<td><label for="location">locatie:</label></td>
	<td><input class="m" name="location" id="location" type="text" value="<?php echo $location; ?>" /></td>
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
		<td><input class="s" name="time[<?php echo $values['id']; ?>]" id="time<?php echo $values['id']; ?>" type="text" value="<?php echo $values['time']; ?>" /></td>
		<td><textarea name="description[<?php echo $values['id']; ?>]" id="description<?php echo $values['id']; ?>" rows="4" cols="40"><?php echo $values['description']; ?></textarea></td>
		<td><input class="m" name="contact[<?php echo $values['id']; ?>]" id="contact<?php echo $values['id']; ?>" type="text" value="<?php echo $values['contact']; ?>" /></td>
	</tr>
	<?php
}
?>

<tr>
	<td>&nbsp;</td>
	<td colspan="3"><input type="checkbox" name="closed" id="closed" value="true"<?php if ($open == 0) echo ' checked="checked"';  ?> /><label for="closed">melding afsluiten</label></td>
</tr>
</table>

<p><input type="submit" name"add" value="nog een tijdstip toevoegen"> <input type="submit" name"save" value="opslaan en naar overzicht"> <a href=?>Annuleren</a></p>

</form>