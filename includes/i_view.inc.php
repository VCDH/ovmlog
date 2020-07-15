<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/
?>

<h1>Incident</h1>

<?php

//decide edit or add
if (!empty($_GET['id'])) {
    $qry = "SELECT `".$sql['table_i']."`.`date` AS `date`,
    `".$sql['table_i']."`.`road` AS `road`,
    `".$sql['table_i']."`.`location` AS `location`,
    `".$sql['table_i']."`.`scenario` AS `scenario`,
    `".$sql['table_i']."`.`open` AS `open`,
    `".$sql['table_i']."`.`review` AS `review`,
    `C`.`username` AS `username_create`,
    `E`.`username` AS `username_edit`
	FROM `".$sql['database']."`.`".$sql['table_i']."`
    LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."` AS `C`
    ON `".$sql['table_i']."`.`user_id_create` = `C`.`id`
    LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."` AS `E`
    ON `".$sql['table_i']."`.`user_id_edit` = `E`.`id`
	WHERE `".$sql['table_i']."`.`id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'
	LIMIT 1";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res)) {
		$edit = TRUE;
	}
}

if ($edit === TRUE) {
	//load values
	$data = mysqli_fetch_assoc($res);
	
	$date = date('d-m-Y', strtotime($data['date']));
	$road = htmlspecialchars($data['road'], ENT_SUBSTITUTE);
	$location = htmlspecialchars($data['location'], ENT_SUBSTITUTE);
	$scenario = htmlspecialchars($data['scenario'], ENT_SUBSTITUTE);
	$open = $data['open'];
    $review = $data['review'];
    $regelaanpak = $data['regelaanpak'];
    $username_create = htmlspecialchars($data['username_create'], ENT_SUBSTITUTE);
    $username_edit = htmlspecialchars($data['username_edit'], ENT_SUBSTITUTE);
	
	$qry = "SELECT 
    `".$sql['table_id']."`.`id`,
    `".$sql['table_id']."`.`time`,
    `".$sql['table_id']."`.`description`,
    `".$sql['table_id']."`.`contact`,
    `C`.`username` AS `username_create`,
    `E`.`username` AS `username_edit` 
	FROM `".$sql['database']."`.`".$sql['table_id']."`
    LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."` AS `C`
    ON `".$sql['table_id']."`.`user_id_create` = `C`.`id`
    LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."` AS `E`
    ON `".$sql['table_id']."`.`user_id_edit` = `E`.`id`
	WHERE `".$sql['table_id']."`.`parent_id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'
	ORDER BY `".$sql['table_id']."`.`time`";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res)) {
		while ($data = mysqli_fetch_assoc($res)) {
			$content[] = array(	'id' => $data['id'], 
								'time' => date('H:i', strtotime($data['time'])), 
								'description' => htmlspecialchars($data['description'], NULL, 'ISO-8859-15'), 
								'contact' => htmlspecialchars($data['contact'], ENT_SUBSTITUTE),
                                'username_create' => htmlspecialchars($data['username_create'], ENT_SUBSTITUTE),
                                'username_edit' => htmlspecialchars($data['username_edit'], ENT_SUBSTITUTE));
		}
	}
	
	?>
	<div class="right"><a href="?p=i&amp;id=<?php echo htmlspecialchars($_GET['id']); ?>">bewerk</a></div>
	
	<table>
    <tr>
        <td><label>datum:</label></td>
        <td><?php echo $date; ?></td>
        <td><label>scenario:</label></td>
        <td colspan="5"><?php echo $scenario; ?></td>
    </tr>
    <tr>
        <td><label>wegnr:</label></td>
        <td><?php echo $road; ?></td>
        <td><label>locatie:</label></td>
        <td><?php echo $location; ?></td>
        <td><label>evaluatie:</label></td>
        <td><?php echo ($review == '1') ? 'Ja' : 'Nee'; ?></td>
        <td><label>regelaanpak:</label></td>
        <td><?php echo ($regelaanpak == '1') ? 'Ja' : 'Nee'; ?></td>
    </tr>
    </table>
    
    <?php 
    echo '<p class="small">Gelogd door: <strong>'.$username_create.'</strong>';
    if ($username_create != $username_edit) {
        echo ' Laatst bewerkt door: <strong>'.$username_edit.'</strong>';
    }
    echo '</p>'; 
    ?>

	<table class="grid">
	<tr>
		<td>&nbsp;</td>
		<td><label>tijd</label></td>
		<td class="expand"><label>beschrijving</label></td>
		<td><label>gesproken met</label></td>
	</tr>

	<?php
	foreach ($content as $count => $values) {
		?>
		<tr>
			<td class="count"><?php echo $count+1; ?></td>
			<td><?php echo $values['time']; ?></td>
			<td class="expand" style="white-space: normal">
            <?php 
            echo nl2br($values['description']); 
            if (($values['username_create'] != $username_create) || ($values['username_edit'] != $username_edit)) {
                echo '<p class="small">Gelogd door: <strong>'.$values['username_create'].'</strong>';
                if ($values['username_create'] != $values['username_edit']) {
                    echo ' Laatst bewerkt door: <strong>'.$values['username_edit'].'</strong>';
                }
                echo '</p>'; 
            }
            ?>
            </td>
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
	<?php
}
else {
	echo '<p class="error">Geen incident met dit id.</p>';
}
?>