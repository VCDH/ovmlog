<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

$max_autocomplete = 50;

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

<script>
$(function() {
	$( "#date_start" ).datetimepicker({altField: "#time_start", stepMinute: 10});
	$( "#date_end" ).datetimepicker({altField: "#time_end", stepMinute: 10});
	
	$( "#scenario" ).autocomplete({
		source: [<?php
			$autocomplete = array();
			$qry = "SELECT DISTINCT `scenario` 
			FROM `".$sql['database']."`.`".$sql['table_w']."`";
			$res = mysqli_query($sql['link'], $qry);
			while ($data = mysqli_fetch_row($res)) {
				if (!empty($data[0])) $autocomplete[] = '"'.htmlspecialchars($data[0]).'"';
			}
			echo implode(',', $autocomplete);
		?>],
		delay: 0,
		minLength: <?php echo floor(count($autocomplete)/$max_autocomplete); ?>
	});
	$( "#road" ).autocomplete({
		source: [<?php
			$autocomplete = array();
			$qry = "SELECT DISTINCT `road` 
			FROM `".$sql['database']."`.`".$sql['table_w']."`";
			$res = mysqli_query($sql['link'], $qry);
			while ($data = mysqli_fetch_row($res)) {
				if (!empty($data[0])) $autocomplete[] = '"'.htmlspecialchars($data[0]).'"';
			}
			echo implode(',', $autocomplete);
		?>],
		delay: 0,
		minLength: <?php echo floor(count($autocomplete)/$max_autocomplete); ?>
	});
});
</script>

<h1><?php echo $title; ?></h1>

<form action="?s=w" method="post">
<input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']) ?>" />

<table>
<tr>
	<td>
		<label for="date_start">van:</label>
	</td><td>
		<input class="s" name="date_start" id="date_start" type="text" value="<?php echo $date_start; ?>" /> 
		<input class="time" name="time_start" id="time_start" type="text" value="<?php echo $time_start; ?>" />
		<label for="date_end">tot:</label>
		<input class="s" name="date_end" id="date_end" type="text" value="<?php echo $date_end; ?>" /> <input class="time" name="time_end" id="time_end" type="text" value="<?php echo $time_end; ?>" />
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
		<input type="radio" name="scenario" id="scenario_nieuw" value="nieuw"<?php if ($scenario == 'nieuw') echo 'checked="checked"'; ?> /><label for="scenario_nieuw">nieuw</label>
		<input type="radio" name="scenario" id="scenario_hergebruik" value="hergebruik"<?php if ($scenario == 'hergebruik') echo 'checked="checked"'; ?> /><label for="scenario_hergebruik">hergebruik</label>
		<input type="radio" name="scenario" id="scenario_nee" value="nee"<?php if ($scenario == 'nee') echo 'checked="checked"'; ?> /><label for="scenario_nee">nee</label>
		<input type="radio" name="scenario" id="scenario_ntb" value="ntb"<?php if (($scenario != 'nieuw') && ($scenario != 'nee') && ($scenario != 'hergebruik')) echo 'checked="checked"'; ?> /><label for="scenario_ntb">ntb</label>
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