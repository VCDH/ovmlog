<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

?>

<h1>Werkzaamheden</h1>

<?php

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
	$data = mysqli_fetch_assoc($res);
	
	$datetime_start = date('d-m-Y H:i', strtotime($data['datetime_start']));
	$datetime_end = date('d-m-Y H:i', strtotime($data['datetime_end']));
	$road = htmlspecialchars($data['road']);
	$location = htmlspecialchars($data['location']);
	$description = htmlspecialchars($data['description']);
	$scenario = htmlspecialchars($data['scenario']);
	
	?>
	<div class="right"><a href="?p=w&amp;id=<?php echo htmlspecialchars($_GET['id']); ?>">bewerk</a></div>

	<table>
	<tr>
		<td>
			<label>van:</label>
		</td><td>
			<?php echo $datetime_start; ?>
			<label>tot:</label>
			<?php echo $datetime_end; ?>
		</td>
	</tr>
	<tr>
		<td>
			<label>wegnr:</label>
		</td><td>
			<?php echo $road; ?>
			<label>locatie:</label>
			<?php echo $location; ?>
		</td>
	</tr>
	<tr>
		<td>
			<label>scenario:</label>
		</td><td>
			<?php echo $scenario; ?>
		</td>
	</tr>
	<tr>
		<td>
			<label for="description">beschrijving:</label>
		</td><td>
			<?php echo nl2br($description); ?>
		</td>
	</tr>
	</table>
	
	<p><a href="?p=i_hist">Terug naar overzicht</a></p>
	<?php
}
else {
	echo '<p class="error">Geen werkzaamheden met dit id.</p>';
}
?>