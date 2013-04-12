<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/
?>

<h2>Incidenten analyse</h2>
<p class="noprint"><a href="?p=i_hist">&laquo; terug</a></p>
<form action="?p=i_analyse" method="post">
<input type="radio" id="week" name="period" value="week"<?php if ($_POST['period'] != 'month') echo ' checked="checked"' ?> /> <label for="week">Week</label> <input type="radio" id="month" name="period" value="month"<?php if ($_POST['period'] == 'month') echo ' checked="checked"' ?> /> <label for="month">Maand</label><br />
<input type="radio" id="overview" name="type" value="overview"<?php if (($_POST['type'] != 'scenarios') && ($_POST['type'] != 'roads')) echo ' checked="checked"' ?> /> <label for="overview">Overzicht</label> <input type="radio" id="scenarios" name="type" value="scenarios"<?php if ($_POST['type'] == 'scenarios') echo ' checked="checked"' ?> /> <label for="scenarios">Scenarios</label> <input type="radio" id="roads" name="type" value="roads"<?php if ($_POST['type'] == 'roads') echo ' checked="checked"' ?> /> <label for="roads">Wegen</label><br />
<input class="noprint" type="submit" value="Weergeven" />
</form>
<br />

<?php
/*
 * PROCESS DATA
*/

ob_start();
setlocale(LC_TIME, '', 'nl_NL', 'nld_nld');
set_time_limit(240);
$table = array();
$types = array();

//query
if ($_POST['type'] == 'scenarios') {
	$qry = "SELECT YEAR(`date`), ";
	if ($_POST['period'] == 'month') $qry .= "MONTH(`date`)";
	else $qry .= "WEEK(`date`)";
	$qry .= ", COUNT(*), `scenario`
	FROM `".$sql['database']."`.`".$sql['table_i']."`
	GROUP BY `scenario`, ";
	if ($_POST['period'] == 'month') $qry .= "MONTH(`date`)";
	else $qry .= "WEEK(`date`)";
	$res = mysqli_query($sql['link'], $qry);
	while ($row = mysqli_fetch_row($res)) {
		if (!empty($row[3]) && ($row[3] != 'maatwerk')) {
			if ($row[1] < 10) $row[1] = '0'.$row[1];
			$table[$row[0].'-'.$row[1]][$row[3]] = $row[2];
			if (!in_array($row[3], $types)) $types[] = $row[3];
		}
	}
}
elseif ($_POST['type'] == 'roads') {
	$qry = "SELECT YEAR(`date`), ";
	if ($_POST['period'] == 'month') $qry .= "MONTH(`date`)";
	else $qry .= "WEEK(`date`)";
	$qry .= ", COUNT(*), `road`
	FROM `".$sql['database']."`.`".$sql['table_i']."`
	WHERE `scenario` != ''
	GROUP BY `road`, ";
	if ($_POST['period'] == 'month') $qry .= "MONTH(`date`)";
	else $qry .= "WEEK(`date`)";
	$res = mysqli_query($sql['link'], $qry);
	while ($row = mysqli_fetch_row($res)) {
		if (!empty($row[3]) && ($row[3] != 'maatwerk')) {
			if ($row[1] < 10) $row[1] = '0'.$row[1];
			$table[$row[0].'-'.$row[1]][$row[3]] = $row[2];
			if (!in_array($row[3], $types)) $types[] = $row[3];
		}
	}
}
else {
	$qry = "SELECT YEAR(`date`), ";
	if ($_POST['period'] == 'month') $qry .= "MONTH(`date`)";
	else $qry .= "WEEK(`date`)";
	$qry .= ", COUNT(*)
	FROM `".$sql['database']."`.`".$sql['table_i']."`
	WHERE `scenario` = 'maatwerk'
	GROUP BY YEAR(`date`), ";
	if ($_POST['period'] == 'month') $qry .= "MONTH(`date`)";
	else $qry .= "WEEK(`date`)";
	$res = mysqli_query($sql['link'], $qry);
	while ($row = mysqli_fetch_row($res)) {
		if ($row[1] < 10) $row[1] = '0'.$row[1];
		$table[$row[0].'-'.$row[1]]['maatwerk'] = $row[2];
	}
	$types[] = 'maatwerk';

	$qry = "SELECT YEAR(`date`), ";
	if ($_POST['period'] == 'month') $qry .= "MONTH(`date`)";
	else $qry .= "WEEK(`date`)";
	$qry .= ", COUNT(*)
	FROM `".$sql['database']."`.`".$sql['table_i']."`
	WHERE `scenario` = ''
	GROUP BY YEAR(`date`), ";
	if ($_POST['period'] == 'month') $qry .= "MONTH(`date`)";
	else $qry .= "WEEK(`date`)";
	$res = mysqli_query($sql['link'], $qry);
	while ($row = mysqli_fetch_row($res)) {
		if ($row[1] < 10) $row[1] = '0'.$row[1];
		$table[$row[0].'-'.$row[1]]['geen'] = $row[2];
	}
	$types[] = 'geen';

	$qry = "SELECT YEAR(`date`), ";
	if ($_POST['period'] == 'month') $qry .= "MONTH(`date`)";
	else $qry .= "WEEK(`date`)";
	$qry .= ", COUNT(*)
	FROM `".$sql['database']."`.`".$sql['table_i']."`
	WHERE `scenario` != 'maatwerk'
	AND `scenario` != ''
	GROUP BY YEAR(`date`), ";
	if ($_POST['period'] == 'month') $qry .= "MONTH(`date`)";
	else $qry .= "WEEK(`date`)";
	$res = mysqli_query($sql['link'], $qry);
	while ($row = mysqli_fetch_row($res)) {
		if ($row[1] < 10) $row[1] = '0'.$row[1];
		$table[$row[0].'-'.$row[1]]['scenario'] = $row[2];
	}
	$types[] = 'scenario';
}

ksort($table);

//json cols
$json = "{\n";
$json .= "cols: [\n";
//date
$json .= "{id: 'dates', label: 'datum', type: 'string'}";
//values
foreach ($types as $type) {
	$json .= ", {id: '".$type."', label: '".$type."', type: 'number'}";
}
$json .= "],\n";

//json rows
$json .= "rows: [\n";
foreach ($table as $date => $row) {
	$json .= "{c:[";
	//date
	$json .= "{v: '".$date."'}";
	//values
	foreach ($types as $type) {
		$json .= ", {v: ";
		if (empty($row[$type])) {
			//$json .= 'null';
			$json .= 0;
		}
		else {
			$json .= $row[$type];
		}
		$json .= "}";
	}
	$json .= "]},\n";
}
$json .= "]\n";
$json .= "}\n";

?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
	var data = new google.visualization.DataTable(
<?php echo $json; ?>
);

	var options = {
	  title: 'Grafiek',
	  hAxis: {title: '<?php if ($_POST['period'] == 'month') echo 'jaar-maand'; else echo 'jaar-week'; ?>', slantedText:true, slantedTextAngle:90},
	  vAxis: {title: 'aantal'},
	  chartArea: {left:60,top:60,width:'80%',height:500}
	};

	var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
	chart.draw(data, options);
  }
</script>
<div id="chart_div" style="width: 100%; height: 640px;"></div>