<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

$max_autocomplete = 50;

//decide edit or add
if (!empty($_GET['id'])) {
	$qry = "SELECT * 
	FROM `".$sql['database']."`.`".$sql['table_e']."`
	WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'
	LIMIT 1";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res)) {
		$edit = TRUE;
	}
}

if ($edit === TRUE) {
	//load values
	$title = 'Evenement bewerken';
	$data = mysqli_fetch_assoc($res);
	
	$date_start = date('d-m-Y', strtotime($data['datetime_start']));
	$date_end = date('d-m-Y', strtotime($data['datetime_end']));
	$time_start = date('H:i', strtotime($data['datetime_start']));
	$time_end = date('H:i', strtotime($data['datetime_end']));
	$name = htmlspecialchars($data['name']);
	$description = htmlspecialchars($data['description']);
	$scenario = htmlspecialchars($data['scenario']);
}
else {
	//default values
	$title = 'Evenement toevoegen';
	$time_start = '00:00';
	$time_end = '23:59';
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
			FROM `".$sql['database']."`.`".$sql['table_e']."`";
			$res = mysqli_query($sql['link'], $qry);
			while ($data = mysqli_fetch_row($res)) {
				if (!empty($data[0])) $autocomplete[] = '"'.htmlspecialchars($data[0]).'"';
			}
			echo implode(',', $autocomplete);
		?>],
		delay: 0,
		minLength: <?php echo floor(count($autocomplete)/$max_autocomplete); ?>
	});
	$( "#name" ).autocomplete({
		source: [<?php
			$autocomplete = array();
			$qry = "SELECT DISTINCT `name` 
			FROM `".$sql['database']."`.`".$sql['table_e']."`";
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

<form action="?s=e" method="post">
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
		<label for="scenario">scenario:</label>
	</td><td>
		<?php
		$scenario_status = array('ntb', 'nee', 'hergebruik', 'nieuw', 'voorbereid', 'geprogrammeerd', 'geactiveerd');
		echo '<select name="scenario" id="scenario">';
		foreach ($scenario_status as $status) {
			echo '<option value="';
			echo $status;
			echo '"';
			if ($scenario == $status) echo ' selected="selected"';
			echo '>';
			echo $status;
			echo '</option>';
		}
		echo '</select>';
		?>
	</td>
</tr>
<tr>
	<td>
		<label for="name">naam:</label>
	</td><td>
		<input class="l" name="name" id="name" type="text" value="<?php echo $name; ?>" />
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