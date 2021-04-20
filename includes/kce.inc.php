<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2016
*/
setlocale(LC_ALL, 'Dutch_Netherlands', 'Dutch', 'nl_NL', 'nl', 'nl_NL.ISO8859-1', 'nld_NLD', 'nl_NL.utf8');

if (!($review_date_start = strtotime($_POST['review_date_start']))) {
    $review_date_start = time() - 60*60*24*14;
}
if (!($review_date_end = strtotime($_POST['review_date_end']))) {
    $review_date_end = time();
}
if (!($upcoming_date_start = strtotime($_POST['upcoming_date_start']))) {
    $upcoming_date_start = time();
}
if (!($upcoming_date_end = strtotime($_POST['upcoming_date_end']))) {
    $upcoming_date_end = time() + 60*60*24*14;
}
?>

<script>
$(function() {
	$( "#review_date_start" ).datepicker();
	$( "#review_date_end" ).datepicker();
	$( "#upcoming_date_start" ).datepicker();
	$( "#upcoming_date_end" ).datepicker();
});
</script>

<h1>KCE</h1>
<p class="noprint"><a href="?">Terug naar beginpagina</a></p>
<form method="post">
<table>
<tr><th colspan="2">Terugkijken</th><th colspan="2">Vooruitkijken</th></tr>
<tr><td>van:</td><td><input class="s" name="review_date_start" id="review_date_start" type="text" value="<?php echo date('d-m-Y', $review_date_start); ?>" /></td><td>van:</td><td><input class="s" name="upcoming_date_start" id="upcoming_date_start" type="text" value="<?php echo date('d-m-Y', $upcoming_date_start); ?>" /></td></tr>
<tr><td>t/m:</td><td><input class="s" name="review_date_end" id="review_date_end" type="text" value="<?php echo date('d-m-Y', $review_date_end); ?>" /></td><td>t/m:</td><td><input class="s" name="upcoming_date_end" id="upcoming_date_end" type="text" value="<?php echo date('d-m-Y',$upcoming_date_end); ?>" /></td></tr>
</table>
<p class="noprint"><input type="submit" value="OK" /></p>
</form>

<h1>Daglogging</h1>

<?php
$qry = "SELECT `".$sql['table_d']."`.`id` AS `id`, `datetime`, `description`, `".$sql['table_users']."`.`username` AS `username`, `".$sql['table_users']."`.`id` AS `user_id`
	FROM `".$sql['database']."`.`".$sql['table_d']."`
	LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."`
	ON `".$sql['table_users']."`.`id` = `".$sql['table_d']."`.`user_id_create`
    WHERE DATE(`datetime`) BETWEEN '".date('Y-m-d', $review_date_start)."' AND '".date('Y-m-d', $review_date_end)."'
    AND `review` = TRUE
	ORDER BY `datetime` ASC, `".$sql['table_d']."`.`id` ASC";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
    while ($row = mysqli_fetch_assoc($res)) {
        echo '<tr><td>';
        echo strtolower(strftime("%a %e %b %H:%M", strtotime($row['datetime'])));
        echo '</td><td class="expand">';
        echo htmlspecialchars($row['description'], ENT_SUBSTITUTE);
        echo '</td><td>';
        echo htmlspecialchars($row['username'], ENT_SUBSTITUTE);
        echo '</td></tr>';
    }
	echo '</table>';
}
else {
    echo '<p>Geen daglogging voor deze datum</p>';
}
?>

<h1>Incidenten</h1>

<?php
$incident_details = array();
$qry = "SELECT `id`, `date`, `road`, `location`, `scenario`, `review`  
	FROM `".$sql['database']."`.`".$sql['table_i']."`
    WHERE `date` BETWEEN '".date('Y-m-d', $review_date_start)."' AND '".date('Y-m-d', $review_date_end)."'
	ORDER BY `date` DESC";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th>datum</th><th>tijd</th><th>locatie/omschrijving</th><th>scenario</th><th>evaluatie</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		$qry2 = "SELECT `time`
		FROM `".$sql['database']."`.`".$sql['table_id']."`
		WHERE `parent_id` = ".$row[0]."
		ORDER BY `id`
		LIMIT 1";
		$res2 = mysqli_query($sql['link'], $qry2);
		if (mysqli_num_rows($res2)) {
			$row2 = mysqli_fetch_row($res2);
		}
		echo '<tr><td>'.date('d-m-Y', strtotime($row[1])).'</td><td>'.date('H:i', strtotime($row2[0])).'</td><td class="expand"><a href="?p=i_view&amp;id='.$row[0].'">'.htmlspecialchars($row[2].' - '.$row[3]).'</a></td><td>'.htmlspecialchars($row[4]).'</td><td>'.(($row[5] == '1') ? 'Ja' : '').'</td></tr>';
        //build list for detailed view
        if (($row[5] == '1') || ($row[4] == 'maatwerk')) {
            $incident_details[] = $row[0];
        }
	}
	echo '</table>';
}
else {
	?>
	<p>Geen incidenten.</p>
	<?php
}

if (!empty($incident_details)) {
    echo '<h1>Details incidenten</h1>';
    foreach ($incident_details as $id) {
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
        WHERE `".$sql['table_i']."`.`id` = '".mysqli_real_escape_string($sql['link'], $id)."'
        LIMIT 1";
        $res = mysqli_query($sql['link'], $qry);
        $data = mysqli_fetch_assoc($res);
        
        $date = date('d-m-Y', strtotime($data['date']));
        $road = htmlspecialchars($data['road']);
        $location = htmlspecialchars($data['location']);
        $scenario = htmlspecialchars($data['scenario']);
        $open = $data['open'];
        $review = $data['review'];
        $username_create = htmlspecialchars($data['username_create']);
        $username_edit = htmlspecialchars($data['username_edit']);
        
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
        WHERE `".$sql['table_id']."`.`parent_id` = '".mysqli_real_escape_string($sql['link'], $id)."'
        ORDER BY `".$sql['table_id']."`.`time`";
        $res = mysqli_query($sql['link'], $qry);
        if (mysqli_num_rows($res)) {
           $content = array();
           while ($data = mysqli_fetch_assoc($res)) {
                $content[] = array(	'id' => $data['id'], 
                                    'time' => date('H:i', strtotime($data['time'])), 
                                    'description' => htmlspecialchars($data['description'], NULL, 'ISO-8859-15'), 
                                    'contact' => htmlspecialchars($data['contact']),
                                    'username_create' => htmlspecialchars($data['username_create']),
                                    'username_edit' => htmlspecialchars($data['username_edit']));
            }
        }
        
        ?>
        <h2>Incident #<?php echo $id; ?></h2>
        
        <table>
        <tr>
            <td><label>datum:</label></td>
            <td><?php echo $date; ?></td>
            <td><label>scenario:</label></td>
            <td<?php if ($review == '1') echo ' colspan="3"'; ?>><?php echo $scenario; ?></td>
        </tr>
        <tr>
            <td><label>wegnr:</label></td>
            <td><?php echo $road; ?></td>
            <td><label>locatie:</label></td>
            <td><?php echo $location; ?></td>
            <?php if ($review == '1') { ?>
            <td><label>evaluatie:</label></td>
            <td>Ja</td>
            <?php } ?>
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
        </table>
        <?php
    }
}
?>

<hr />
<h1>Werkzaamheden en evenementen afgelopen periode</h1>

<?php
$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `road`, `location`, `scenario`, `type`, `name`, `spare` 
	FROM `".$sql['database']."`.`".$sql['table_p']."`
	WHERE 
	`datetime_start` BETWEEN '".date('Y-m-d', $review_date_start)."' AND '".date('Y-m-d', $review_date_end)."'
    OR `datetime_end` BETWEEN '".date('Y-m-d', $review_date_start)."' AND '".date('Y-m-d', $review_date_end)."'
    OR (`datetime_start` < '".date('Y-m-d', $review_date_start)."' AND `datetime_end` > '".date('Y-m-d', $review_date_end)."')
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th></th><th>start</th><th>eind</th><th>locatie/naam</th><th title="reserve">res</th><th title="scenario">scn</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr'.(($row[5]=='nee')?' class="low"':'').'>';
        echo '<td><img src="'.(($row[6] == 'w') ? 'werk' : 'evenement').'.png" width="16" height="16" alt="'.(($row[6] == 'w') ? 'werk' : 'evenement').'" title="'.(($row[6] == 'w') ? 'werk' : 'evenement').'" /></td>';
        echo '<td>'.
        ((date('Y')==date('Y',strtotime($row[1])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[1])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[1]))))).
        '</td><td>'.
        ((date('Y')==date('Y',strtotime($row[2])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[2])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[2]))))).
        '</td><td class="expand"><a href="?p=p_view&amp;id='.$row[0].'">';
		if (!empty($row[7])) echo htmlspecialchars($row[7]);
        elseif (empty($row[3]) && empty($row[4])) echo '(leeg)';
		else echo htmlspecialchars($row[3].' - '.$row[4]);
		echo '</a></td><td>';
		echo (($row[8] == '1') ? 'ja' : '');
		echo '</td><td>';
		echo htmlspecialchars($row[5]);
		echo '</td></tr>';
	}
	echo '</table>';
}
else {
    echo '<p>Er zijn geen werkzaamheden of evenementen.</p>';
}
?>

<hr />
<h1>Geplande werkzaamheden en evenementen komende periode</h1>

<?php
$qry = "SELECT `id`, `datetime_start`, `datetime_end`, `road`, `location`, `scenario`, `type`, `name`, `spare`  
	FROM `".$sql['database']."`.`".$sql['table_p']."`
	WHERE 
	`datetime_start` BETWEEN '".date('Y-m-d', $upcoming_date_start)."' AND '".date('Y-m-d', $upcoming_date_end)."'
    OR `datetime_end` BETWEEN '".date('Y-m-d', $upcoming_date_start)."' AND '".date('Y-m-d', $upcoming_date_end)."'
    OR (`datetime_start` < '".date('Y-m-d', $upcoming_date_start)."' AND `datetime_end` > '".date('Y-m-d', $upcoming_date_end)."')
	ORDER BY `datetime_start`";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
	echo '<tr><th></th><th>start</th><th>eind</th><th>locatie</th><th title="reserve">res</th><th title="scenario">scn</th></tr>';
	while ($row = mysqli_fetch_row($res)) {
		echo '<tr'.((strtotime($row[1])<time()+604800)?' class="upcoming"':'').'>';
        echo '<td><img src="'.(($row[6] == 'w') ? 'werk' : 'evenement').'.png" width="16" height="16" alt="'.(($row[6] == 'w') ? 'werk' : 'evenement').'" title="'.(($row[6] == 'w') ? 'werk' : 'evenement').'" /></td>';
        echo '<td>'.
        ((date('Y')==date('Y',strtotime($row[1])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[1])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[1]))))).
        '</td><td>'.
        ((date('Y')==date('Y',strtotime($row[2])))?(strtolower(strftime("%a %e %b %H:%M", strtotime($row[2])))):(strtolower(strftime("%a %e %b %G %H:%M", strtotime($row[2]))))).
        '</td><td class="expand"><a href="?p=p_view&amp;id='.$row[0].'">';
		if (!empty($row[7])) echo htmlspecialchars($row[7]);
        elseif (empty($row[3]) && empty($row[4])) echo '(leeg)';
		else echo htmlspecialchars($row[3].' - '.$row[4]);
		echo '</a></td><td>';
		echo (($row[8] == '1') ? 'ja' : '');
		echo '</td><td>';
		echo htmlspecialchars($row[5]);
		echo '</td></tr>';
	}
	echo '</table>';
}
else {
    echo '<p>Er zijn geen geplande werkzaamheden of evenementen.</p>';
}
?>
