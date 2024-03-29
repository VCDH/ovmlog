<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2016-2023
*/

//controleer of mag bekijken
if (permissioncheck('bekijk_werk_evn') !== true) {
	echo 'niet toegestaan';
	exit;
}

$max_autocomplete = 50;

//decide edit or add
if (!empty($_GET['id'])) {
	$qry = "SELECT * 
	FROM `".$sql['database']."`.`".$sql['table_p']."`
	WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_GET['id'])."'
	LIMIT 1";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res)) {
		$edit = TRUE;
	}
}
elseif (is_numeric($_GET['copyfrom'])) {
	$qry = "SELECT * 
	FROM `".$sql['database']."`.`".$sql['table_p']."`
	WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_GET['copyfrom'])."'
	LIMIT 1";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_num_rows($res)) {
		$edit = TRUE;
	}
}

if ($edit === TRUE) {
	//load values
	$title = 'Werkzaamheden/evenement bewerken';
	$data = mysqli_fetch_assoc($res);
	
	$date_start = date('d-m-Y', strtotime($data['datetime_start']));
	$date_end = date('d-m-Y', strtotime($data['datetime_end']));
	//if dates are 01-01-1970, set to empty string
	if ($date_start == '01-01-1970') {
		$date_start = '';
		$date_end = '';
	}
	$time_start = date('H:i', strtotime($data['datetime_start']));
	$time_end = date('H:i', strtotime($data['datetime_end']));
	$road = htmlspecialchars($data['road'], ENT_SUBSTITUTE);
	$location = htmlspecialchars($data['location'], ENT_SUBSTITUTE);
	$description = htmlspecialchars($data['description'], NULL, 'ISO-8859-15');
	$spare = $data['spare'];
	$scenario = htmlspecialchars($data['scenario'], ENT_SUBSTITUTE);
	//status 'reserve' is vervallen, hiermee wordt radiorondje gezet als dit niet al zo is.
	if ($scenario == 'reserve') {
		$spare = 1;
		$scenario = 'ntb';
	}
	$scenario_naam = htmlspecialchars($data['scenario_naam'], ENT_SUBSTITUTE);
    $name = htmlspecialchars($data['name'], ENT_SUBSTITUTE); 
	$type = $data['type'];
	$user_id_assigned = $data['user_id_assigned'];
}
else {
	//default values
	$title = 'Werkzaamheden/evenement toevoegen';
	$time_start = '21:00';
	$time_end = '06:00';
    $scenario = 'ntb';
}
?>

<script>
$(function() {
	$( "#date_start" ).datetimepicker({altField: "#time_start", stepMinute: 5});
	$( "#date_end" ).datetimepicker({altField: "#time_end", stepMinute: 5});
	
	$( "#scenario" ).autocomplete({
		source: [<?php
			$autocomplete = array();
			$qry = "SELECT DISTINCT `scenario` 
			FROM `".$sql['database']."`.`".$sql['table_w']."`";
			$res = mysqli_query($sql['link'], $qry);
			while ($data = mysqli_fetch_row($res)) {
				if (!empty($data[0])) $autocomplete[] = '"'.addslashes(htmlspecialchars($data[0], ENT_SUBSTITUTE)).'"';
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
				if (!empty($data[0])) $autocomplete[] = '"'.addslashes(htmlspecialchars($data[0], ENT_SUBSTITUTE)).'"';
			}
			echo implode(',', $autocomplete);
		?>],
		delay: 0,
		minLength: <?php echo floor(count($autocomplete)/$max_autocomplete); ?>
	});
	//no date
	$("#date_start").change(function () {
		if ($("#date_start").length > 0) {
			$("#nodate").prop('checked', false);
		}
	});
	$("#nodate").change(function () {
		if ($("#nodate").prop('checked') == true) {
			$("#date_start").val(null);
			$("#date_end").val(null);
		}
	});
});
</script>

<h1><?php echo $title; ?></h1>

<form action="?s=p" method="post">
<input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']) ?>" />

<table>
<tr>
	<td>
		<label for="type">type:</label>
	</td><td>
		<?php
		$type_list = array('w' => 'werkzaamheden', 'e' => 'evenement');
		echo '<select name="type" id="type">';
		foreach ($type_list as $type_id => $type_name) {
			echo '<option value="';
			echo $type_id;
			echo '"';
			if ($type_id == $type) echo ' selected="selected"';
			echo '>';
			echo $type_name;
			echo '</option>';
		}
		echo '</select>';
		?>
	</td>
</tr>
<tr>
	<td>
		<label for="date_start">van:</label>
	</td><td>
		<input class="s" name="date_start" id="date_start" type="text" value="<?php echo $date_start; ?>" /> 
		<input class="time" name="time_start" id="time_start" type="text" value="<?php echo $time_start; ?>" />
		<label for="date_end">tot:</label>
		<input class="s" name="date_end" id="date_end" type="text" value="<?php echo $date_end; ?>" /> <input class="time" name="time_end" id="time_end" type="text" value="<?php echo $time_end; ?>" /> 
		<input type="checkbox" name="nodate" value="true" id="nodate"<?php if (empty($date_start)) echo ' checked="checked"'; ?>><label for="nodate">Datum onbekend</label>
		Reserve: <input type="radio" name="spare" value="0" id="spare_0"<?php if ($spare != '1') echo ' checked="checked"'; ?>><label for="spare_0">Nee</label> <input type="radio" name="spare" value="1" id="spare_1"<?php if ($spare == '1') echo ' checked="checked"'; ?>><label for="spare_1">Ja</label>
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
		<label for="name">naam*:</label>
	</td><td>
		<input class="l" name="name" id="name" type="text" value="<?php echo $name; ?>" />
	</td>
</tr>
<tr>
	<td>
		<label for="scenario">scenario status:</label>
	</td><td>
		<?php
		$scenario_status = array('nieuw', 'hergebruik', 'derden-nieuw', 'derden-review', 'derden-definitief', 'nee', 'ntb', 'voorbereid', 'geprogrammeerd', 'geactiveerd', 'DVM-Exchange', 'PZH-Deelscenario', 'handmatig', 'monitoren');
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
		<label for="scenario_naam">scenario naam:</label>
	</td><td>
		<input class="m" name="scenario_naam" id="scenario_naam" type="text" value="<?php echo $scenario_naam; ?>" />
	</td>
</tr>
<tr>
	<td>
		<label for="description">beschrijving:</label>
	</td><td>
		<textarea class="l" name="description" id="description" rows="4" cols="40"><?php echo $description; ?></textarea>
	</td>
</tr>
<tr>
	<td>
		<label for="user_id_assigned">toegewezen aan:</label>
	</td><td>
		<?php
		echo '<select name="user_id_assigned" id="user_id_assigned">';
		echo '<option value="' . getuser('id') . '">' . htmlspecialchars(getuser('name')) . '</option>';
		echo '<option value="0" disabled>---------------</option>';
		echo '<option value="0">(niemand)</option>';
		//get users from database
		$qry = "SELECT `id`, `username` 
		FROM `".$sql['database']."`.`".$sql['table_users']."`
		WHERE `disabled` = FALSE
		ORDER BY `username`";
		$res = mysqli_query($sql['link'], $qry);
		while ($row = mysqli_fetch_row($res)) {
			echo '<option value="';
			echo htmlspecialchars($row[0]);
			echo '"';
			if ($row[0] == $user_id_assigned) echo ' selected="selected"';
			echo '>';
			echo htmlspecialchars($row[1]);
			echo '</option>';
		}
		echo '</select>';
		?>
	</td>
</tr>
</table>

<p><input type="submit" name="saveandview" value="opslaan en bekijken"> <input type="submit" name="save" value="opslaan en naar overzicht"> <?php if (empty($_GET['id'])) { ?><input type="submit" name="saveandcopy" value="opslaan en kopie maken"><?php } ?> <a href="?">Annuleren</a></p>

</form>
<p><br /></p>
<p>*) <i>naam</i> is de naam van het evenement/werkzaamheden als dit niet met een wegnummer/locatie te omschrijven is. Anders leeg laten.</p>
<h2>Verklaring scenario status:</h2>
<table>
<tr><td>                </td><td><b>Fase 1</b>                </td></tr>
<tr><td>Nieuw           </td><td>= er moet een nieuw scenario gemaakt worden                </td></tr>
<tr><td>Hergebruik      </td><td>= er is al een scenario in de scenariobrowser dat misschien nog een beetje aangepast moet worden</td></tr>
<tr><td>Derden-nieuw    </td><td>= er wordt een nieuw scenario gemaakt door een externe partij</td></tr>
<tr><td>Derden-review   </td><td>= scenario door externe partij is in review</td></tr>
<tr><td>Derden-definitief</td><td>= scenario door externe partij is gereed, maar moet nog in scenariobrowser worden gezet</td></tr>
<tr><td>Nee             </td><td>= er is geen scenario nodig                                 </td></tr>
<tr><td>Ntb             </td><td>= nader te bepalen of een scenario nodig is, alleen als je het echt niet weet                </td></tr>
<tr><td>                </td><td><b>Fase 2</b>                </td></tr>
<tr><td>Voorbereid      </td><td>= Een scenario is voorbereid in de scenariobrowser, maar staat nog niet in MM</td></tr>
<tr><td>                </td><td><b>Fase 3</b>                </td></tr>
<tr><td>Geprogrammeerd  </td><td>= Staat gereed in MM om geactiveerd te worden                         </td></tr>
<tr><td>                </td><td><b>Fase 4</b>                </td></tr>
<tr><td>Geactiveerd     </td><td>= Is geactiveerd in MM                                               </td></tr>
<tr><td>DVM-exchange    </td><td>= Staat in MM en DVM-Exchange service is beschikbaar gesteld aan RWS om ingezet te worden door RWS</td></tr>
<tr><td>PZH-Deelscenario</td><td>= Deelscenario voor PZH. Staat in MM en kan door PZH ingezet worden                     </td></tr>
<tr><td>Handmatig       </td><td>= Staat in MM en wordt op verzoek door WVL in/uitgeschakeld. De WVL monitort niet actief of in/uitgeschakeld moet worden.</td></tr>
<tr><td>                </td><td><b>Speciaal</b>                </td></tr>
<tr><td>Monitoren       </td><td>= De situatie moet door de WLV worden gemonitord. In de beschrijving staat waar op gelet moet worden en wie wanneer ge&iuml;nformeerd moet worden en/of wanneer welk scenario wordt ingezet. Als er sprake is van een scenario, dan deze status pas toekennen wanneer het scenario Geprogrammeerd is.</td></tr>
</table>

