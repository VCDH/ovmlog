<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2021
*/

//als geen datum is gegeven, vind de datum van de laatste entry en gebruik die
if (empty($_GET['date'])) {
    $qry = "SELECT `datetime`
        FROM `".$sql['database']."`.`".$sql['table_d']."`
        ORDER BY `id` DESC
        LIMIT 1";
    $res = mysqli_query($sql['link'], $qry);
    $row = mysqli_fetch_row($res);
    $date = date('Y-m-d', strtotime($row[0]));
}
else {
    //decide if a valid date is given
    $date = strtotime($_GET['date']);
    if ($date === FALSE) {
        //use today
        $date = date('Y-m-d');
    }
    else {
        $date = date('Y-m-d', $date);
    }
}


setlocale(LC_ALL, 'Dutch_Netherlands', 'Dutch', 'nl_NL', 'nl', 'nl_NL.ISO8859-1', 'nld_NLD', 'nl_NL.utf8');
?>

<script>
$(function() {
	$( "#date" ).datepicker();
});
</script>

<div class="noprint">

<p><a href="?">&laquo; Terug naar overzicht</a></p>

<form action="?">
<input type="hidden" name="p" value="d_view">
<label for="date">Kies andere datum:</label> <input class="s" name="date" id="date" type="text" value="<?php echo date('d-m-Y', strtotime($date)); ?>"> <input type="submit" value="OK">
</form>

</div>

<h1>Daglogging <?php echo strtolower(strftime("%A %e %B %G", strtotime($date))); ?></h1>



<?php
$qry = "SELECT `datetime`, `description`, `".$sql['table_users']."`.`username`
	FROM `".$sql['database']."`.`".$sql['table_d']."`
	LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."`
	ON `".$sql['table_users']."`.`id` = `".$sql['table_d']."`.`user_id_edit`
    WHERE DATE(`datetime`) = '" . $date . "'
	ORDER BY `".$sql['table_d']."`.`id` ASC";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
    while ($row = mysqli_fetch_row($res)) {
        echo '<tr><td>';
        echo date('H:i', strtotime($row[0]));
        echo '</td><td class="expand">';
        echo htmlspecialchars($row[1], ENT_SUBSTITUTE);
        echo '</td><td>';
        echo ((!empty($row[2])) ? htmlspecialchars($row[2], ENT_SUBSTITUTE) : '');
        echo '</td></tr>';
    }
	echo '</table>';
}
else {
    echo '<p>Geen daglogging voor deze datum</p>';
}
?>