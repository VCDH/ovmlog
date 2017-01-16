<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2016
*/
setlocale(LC_ALL, 'Dutch_Netherlands', 'Dutch', 'nl_NL', 'nl', 'nl_NL.ISO8859-1', 'nld_NLD', 'nl_NL.utf8');

//decide edit or add
if (!empty($_GET['id'])) {
	$qry = "SELECT `".$sql['table_p']."`.`datetime_start` AS `datetime_start`,
    `".$sql['table_p']."`.`datetime_start` AS `datetime_start`,
    `".$sql['table_p']."`.`datetime_end` AS `datetime_end`,
    `".$sql['table_p']."`.`road` AS `road`,
    `".$sql['table_p']."`.`location` AS `location`,
    `".$sql['table_p']."`.`description` AS `description`,
    `".$sql['table_p']."`.`scenario` AS `scenario`,
    `".$sql['table_p']."`.`name` AS `name`,
    `".$sql['table_p']."`.`type` AS `type`,
    `C`.`username` AS `username_create`,
    `E`.`username` AS `username_edit`
	FROM `".$sql['database']."`.`".$sql['table_p']."`
    LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."` AS `C`
    ON `".$sql['table_p']."`.`user_id_create` = `C`.`id`
    LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."` AS `E`
    ON `".$sql['table_p']."`.`user_id_edit` = `E`.`id`
	WHERE `".$sql['table_p']."`.`id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'
	LIMIT 1";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res)) {
		$edit = TRUE;
	}
}

if ($edit === TRUE) {
	//load values
	$data = mysqli_fetch_assoc($res);
	
	$datetime_start = strtolower(strftime("%a %d-%m-%G %H:%M", strtotime($data['datetime_start'])));
	$datetime_end = strtolower(strftime("%a %d-%m-%G %H:%M", strtotime($data['datetime_end'])));
	$road = htmlspecialchars($data['road']);
	$location = htmlspecialchars($data['location']);
	$description = htmlspecialchars($data['description'], NULL, 'ISO-8859-15');
	$scenario = htmlspecialchars($data['scenario']);
    $name = htmlspecialchars($data['name']);
    $type = $data['type'];
    
    if ($type == 'e') echo '<h1>Evenement</h1>';
    else echo '<h1>Werkzaamheden</h1>';
    
	?>
	<div class="right"><a href="?p=p&amp;id=<?php echo htmlspecialchars($_GET['id']); ?>">bewerk</a><br /><a href="?p=p&amp;copyfrom=<?php echo htmlspecialchars($_GET['id']); ?>">dupliceer</a></div>

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
			<label>naam:</label>
		</td><td>
			<?php echo $name; ?>
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
    
    <?php 
    echo '<p class="small">Geplaatst door: <strong>'.htmlspecialchars($data['username_create']).'</strong>';
    if ($data['username_create'] != $data['username_edit']) {
        echo ' Laatst bewerkt door: <strong>'.htmlspecialchars($data['username_edit']).'</strong>';
    }
    echo '</p>'; 
    ?>
	
	<p><a href="?p=p_hist">Terug naar overzicht</a></p>
	<?php
}
else {
	echo '<p class="error">Geen werkzaamheden of evenement met dit id.</p>';
}
?>