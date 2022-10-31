<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2017, 2022
*/
setlocale(LC_ALL, 'Dutch_Netherlands', 'Dutch', 'nl_NL', 'nl', 'nl_NL.ISO8859-1', 'nld_NLD', 'nl_NL.utf8');

//controleer of mag bekijken
if (permissioncheck('bekijk_inc') !== true) {
	echo 'niet toegestaan';
	exit;
}
?>

<h2>Incidenten</h2>
<p><a href="?">&laquo; terug</a> | <a href="?p=i_analyse">analyse</a></p>

<?php
$qry = "SELECT COUNT(*)  
	FROM `".$sql['database']."`.`".$sql['table_i']."`";
$res = mysqli_query($sql['link'], $qry);
$num = mysqli_fetch_row($res);
$num = $num[0];
$step = 100;
if (is_numeric($_GET['start'])) $start = $_GET['start']; else $start = 0;
if ($start > $num) $start = $num - $step;

if ($num) {
	
	//build pagination
	$pag = '<p style="float:right;">'.($start+1).' tot '.min(($start+$step), $num).' van '.$num.'
	| <a href="?p=i_hist&amp;start=0">&laquo; eerste</a>
	| <a href="?p=i_hist&amp;start='.max(0, ($start - $step)).'">&lt; vorige</a>
	| <a href="?p=i_hist&amp;start='.min((floor($num/$step)*$step), max(0, ($start + $step))).'">volgende &gt;</a>
	| <a href="?p=i_hist&amp;start='.(floor($num/$step)*$step).'">laatste &raquo;</a></p>';
	
	$qry = "SELECT `id`, `date`, `road`, `location`, `scenario`, `review`  
		FROM `".$sql['database']."`.`".$sql['table_i']."`
		ORDER BY `date` DESC
		LIMIT ".$start.",".$step;
	$res = mysqli_query($sql['link'], $qry);
	
	echo $pag;
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
		echo '<tr><td>'.strtolower(strftime("%a %e %b %G", strtotime($row[1]))).'</td><td>'.date('H:i', strtotime($row2[0])).'</td><td class="expand"><a href="?p=i_view&amp;id='.$row[0].'">'.htmlspecialchars($row[2].' - '.$row[3], ENT_SUBSTITUTE).'</a></td><td>'.htmlspecialchars($row[4], ENT_SUBSTITUTE).'</td><td>'.(($row[5] == '1') ? 'Ja' : '').'</td></tr>';
	}
	echo '</table>';
	echo $pag;
}
else {
	?>
	<p>Geen incidenten.</p>
	<?php
}
?>