<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

//decide edit or add
if (!empty($_GET['id'])) {
	$qry = "SELECT * 
	FROM `".$sql['database']."`.`".$sql['table_w']."`
	WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'
	LIMIT 1";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res)) {
		$edit = TRUE;
	}
}

if ($edit === TRUE) {
	//load values
	$title = 'Werkzaamheden bewerken';
	$data = mysqli_fetch_assoc($res);
	
	$date_start = date('d-m-Y', strtotime($data['datetime_start']));
	$date_end = date('d-m-Y', strtotime($data['datetime_end']));
	$time_start = date('H:i', strtotime($data['datetime_start']));
	$time_end = date('H:i', strtotime($data['datetime_end']));
	$road = htmlspecialchars($data['road']);
	$location = htmlspecialchars($data['location']);
	$description = htmlspecialchars($data['description']);
	$scenario = htmlspecialchars($data['scenario']);
}
else {
	//default values
	$title = 'Werkzaamheden toevoegen';
	$time_start = '07:00';
	$time_end = '17:00';
}
?>

<h1><?php echo $title; ?></h1>

<form action="?s=w" method="post">
<input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']) ?>" />

<table>
<tr>
	<td>
		<label for="date_start">van:</label>
	</td><td>
		<input class="s" name="date_start" id="date_start" type="text" value="<?php echo $date_start; ?>" /> 
		<input class="s" name="time_start" id="time_start" type="text" value="<?php echo $time_start; ?>" />
		<label for="date_end">tot:</label>
		<input class="s" name="date_end" id="date_end" type="text" value="<?php echo $date_end; ?>" /> <input class="s" name="time_end" id="time_end" type="text" value="<?php echo $time_end; ?>" /></td>
	</td>
</tr>
<tr>
	<td>
		<label for="road">wegnr:</label>
	</td><td>
		<input class="s" name="road" id="road" type="text" value="<?php echo $road; ?>" />
		<label for="location">locatie:</label>
		<input class="m" name="location" id="location" type="text" value="<?php echo $location; ?>" />
	</td>
</tr>
<tr>
	<td>
		<label for="scenario">scenario:</label>
	</td><td>
		<input class="m" name="scenario" id="scenario" type="text" value="<?php echo $scenario; ?>" /> (laat leeg in geval van maatwerk)
	</td>
</tr>
<tr>
	<td>
		<label for="description">beschrijving:</label>
	</td><td>
		<textarea class="l" name="description" id="description" rows="4" cols="40"><?php echo $description; ?></textarea>
	</td>
</tr>
</table>

<p><input type="submit" name"save" value="opslaan en naar overzicht"> <a href="?">Annuleren</a></p>

</form>