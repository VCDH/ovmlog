<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2016, 2022
*/

//controleer of mag bekijken
if (permissioncheck('bekijk_werk_evn') !== true) {
	echo 'niet toegestaan';
	exit;
}

setlocale(LC_ALL, 'Dutch_Netherlands', 'Dutch', 'nl_NL', 'nl', 'nl_NL.ISO8859-1', 'nld_NLD', 'nl_NL.utf8');

include 'functions/generic.fct.php';

//decide edit or add
if (!empty($_GET['id'])) {
	$qry = "SELECT `".$sql['table_p']."`.`id` AS `parent_id`,
	`".$sql['table_p']."`.`datetime_start` AS `datetime_start`,
    `".$sql['table_p']."`.`datetime_start` AS `datetime_start`,
    `".$sql['table_p']."`.`datetime_end` AS `datetime_end`,
    `".$sql['table_p']."`.`road` AS `road`,
    `".$sql['table_p']."`.`location` AS `location`,
    `".$sql['table_p']."`.`description` AS `description`,
    `".$sql['table_p']."`.`scenario` AS `scenario`,
    `".$sql['table_p']."`.`scenario_naam` AS `scenario_naam`,
    `".$sql['table_p']."`.`name` AS `name`,
    `".$sql['table_p']."`.`type` AS `type`,
    `".$sql['table_p']."`.`spare` AS `spare`,
    `A`.`username` AS `username_assigned`,
    `C`.`username` AS `username_create`,
    `E`.`username` AS `username_edit`
	FROM `".$sql['database']."`.`".$sql['table_p']."`
    LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."` AS `A`
    ON `".$sql['table_p']."`.`user_id_assigned` = `A`.`id`
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
	//no date
	if (date('Ymd', strtotime($data['datetime_start'])) == '19700101') {
		$datetime_start = '(onbekend)';
		$datetime_end = '(onbekend)';
	}
	else {
		$datetime_start = strtolower(strftime("%a %d-%m-%G %H:%M", strtotime($data['datetime_start'])));
		$datetime_end = strtolower(strftime("%a %d-%m-%G %H:%M", strtotime($data['datetime_end'])));
	}
	$road = htmlspecialchars($data['road'], ENT_SUBSTITUTE);
	$location = htmlspecialchars($data['location'], ENT_SUBSTITUTE);
	$description = htmlspecialchars($data['description'], NULL, 'ISO-8859-15');
	$spare = $data['spare'];
	$scenario = htmlspecialchars($data['scenario'], ENT_SUBSTITUTE);
	$scenario_naam = htmlspecialchars($data['scenario_naam'], ENT_SUBSTITUTE);
    $name = htmlspecialchars($data['name'], ENT_SUBSTITUTE);
	$type = $data['type'];
	
    
    if ($type == 'e') echo '<h1>Evenement</h1>';
    else echo '<h1>Werkzaamheden</h1>';
    
	?>
	<div class="right">
	<?php 
	if (permissioncheck('bewerk')) { 
		?>
		<a href="?p=p&amp;id=<?php echo htmlspecialchars($_GET['id']); ?>">bewerk</a><br /><a href="?p=p&amp;copyfrom=<?php echo htmlspecialchars($_GET['id']); ?>">dupliceer</a>
		<?php
		//delete only if item is in the future or has no date
		if ((strtotime($data['datetime_start']) > time()) || (date('Ymd', strtotime($data['datetime_start'])) == '19700101')) {
			echo '<br /><a href="?p=p_delete&amp;id=' . htmlspecialchars($_GET['id']) . '">verwijder</a>';
		}
	}
	?>
	</div>

	<table>
	<tr>
		<td>
			<label>van:</label>
		</td><td>
			<?php echo $datetime_start; ?>
			<label>tot:</label>
			<?php echo $datetime_end; ?> 
			<label>Reserve:</label> <?php echo (($spare == '1') ? 'ja' : 'nee'); ?>
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
			<label>scenario&nbsp;status:</label>
		</td><td>
			<?php echo $scenario; ?>
		</td>
	</tr>
    <tr>
		<td>
			<label>scenario&nbsp;naam:</label>
		</td><td>
			<?php 
            if (is_numeric($scenario_naam)) {
                echo '<a href="http://scenariobrowser.vcdh.nl/scenario.php?id=' . $scenario_naam . '" target="_blank">' . $scenario_naam . '</a>';
                }
			elseif (preg_match('/https:\/\/diego\.ndw\.nu\/regelscenarios\/versie\/.+\/.+\/FINAL/i', $scenario_naam)) {
				echo '<a href="' . $scenario_naam . '" target="_blank">' . $scenario_naam . '</a>';
			}
            else {
                echo $scenario_naam; 
            }
            ?>
		</td>
	</tr>
	<tr>
		<td>
			<label>toegewezen&nbsp;aan:</label>
		</td><td>
			<?php echo htmlspecialchars($data['username_assigned']); ?>
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

	<p><a href="?">Terug naar overzicht</a><?php if (permissioncheck('bewerk')) { ?> | <a href="?p=p_hist">Terug naar historielijst</a><?php } ?></p>

	<h2>Bijlagen</h2>
	<ul id="bijlagen">
	</ul>
	<?php 
	if (permissioncheck('bewerk')) { 
		?>
		<form method="post">
		<div id="uploadarea" style="clear:both;">
		<!-- progress bar -->
		<div id="progress">
			<div class="bar" style="width: 0%;"></div>
		</div>
		<!-- The fileinput-button span is used to style the file input field as button -->
		<span class="fileinput-button">
			<span>Upload bestanden...</span>
			<!-- The file input field used as target for the file upload widget -->
			<input id="fileupload" type="file" name="files[]" multiple>
		</span>
		
		<!-- The container for the uploaded files -->
		<div id="files" class="files"></div>
		</div>

		<input type="hidden" name="id" value="<?php echo $data['parent_id']; ?>">
		</form>

		<script src="jquery-file-upload/jquery.iframe-transport.js"></script>
		<script src="jquery-file-upload/jquery.fileupload.js"></script>
		<script src="bijlage.js"></script>
		
		<?php
	}

	?>
	<h2>Gelijktijdige werkzaamheden en evenementen</h2>
	<?php

	//planned with date
	$qry = "SELECT `".$sql['table_p']."`.`id`, `datetime_start`, `datetime_end`, `road`, `location`, `scenario`, `type`, `name`, `spare`, `".$sql['table_users']."`.`username` AS `assigned`   
		FROM `".$sql['database']."`.`".$sql['table_p']."`
		LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."`
		ON `".$sql['table_users']."`.`id` = `".$sql['table_p']."`.`user_id_assigned`
		WHERE (
		`datetime_start` BETWEEN '" .$data['datetime_start'] . "' AND '" .$data['datetime_end'] . "'
		OR `datetime_end` BETWEEN '" .$data['datetime_start'] . "' AND '" .$data['datetime_end'] . "'
		OR (`datetime_start` <= '" .$data['datetime_start'] . "' AND `datetime_end` >= '" .$data['datetime_end'] . "')
		)
		AND `".$sql['table_p']."`.`id` != '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'
		ORDER BY `datetime_start`";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res)) {
		?>
		<table class="grid">
		<thead>
		<tr><th></th><th>start</th><th>eind</th><th>locatie</th><th title="toegewezen aan">toeg.</th><th title="reserve">res</th><th title="scenario">scn</th></tr>
		</thead>
		<tbody class="list">
		<?php
		while ($row = mysqli_fetch_row($res)) {
			display_planned_tablerow($row);
		}
		?>
		</tbody>
		</table>
		<?php
	}
	else {
		echo '<p>Geen gelijktijdige werkzaamheden en evenementen gevonden.</p>';
	}
}
else {
	echo '<p class="error">Geen werkzaamheden of evenement met dit id.</p>';
}
?>