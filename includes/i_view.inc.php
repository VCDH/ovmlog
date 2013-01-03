<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/
?>

<h1>Incident</h1>

<?php

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
	
	?>
	<div class="right"><a href="?p=i&amp;id=<?php echo htmlspecialchars($_GET['id']); ?>">bewerk</a></div>
	
	<table>
	<tr>
		<td><label>datum:</label></td>
		<td><?php echo $date; ?></td>
		<td><label>scenario:</label></td>
		<td><?php echo $scenario; ?></td>
	</tr>
	<tr>
		<td><label>wegnr:</label></td>
		<td><?php echo $road; ?></td>
		<td><label>locatie:</label></td>
		<td><?php echo $location; ?></td>
	</tr>
	</table>

	<table>
	<tr>
		<td>&nbsp;</td>
		<td><label>tijd</label></td>
		<td><label>beschrijving</label></td>
		<td><label>gesproken met</label></td>
	</tr>

	<?php
	foreach ($content as $count => $values) {
		?>
		<tr>
			<td class="count"><?php echo $count+1; ?></td>
			<td><?php echo $values['time']; ?></td>
			<td><?php echo nl2br($values['description']); ?></td>
			<td><?php echo $values['contact']; ?></td>
		</tr>
		<?php
	}
	?>

	<tr>
		<td>&nbsp;</td>
		<td colspan="3"><?php if ($open == 0) echo '<label>melding afgesloten</label>'; else echo '<label>melding open</label>'; ?></td>
	</tr>
	</table>

	<p><a href="?p=i_hist">Terug naar overzicht</a></p>

	</form>
	<?php
}
else {
	echo '<p class="error">Geen incident met dit id.</p>';
}
?>