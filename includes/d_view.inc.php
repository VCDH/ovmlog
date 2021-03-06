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

    $('.daglog-edit').click(function() {
        //hide all edit buttons
        $('.daglog-edit').hide();
        $('.daglog-sticky').hide();
        $('.daglog-review').hide();
        //get contents of current td
        var id = $(this).attr('id').substr(12);
        var entry = $(this).parent().children('.entry');
        var text = entry.text();
        //create edit form and buttons
        var editable_content = $('<form method="post" action="?s=d&amp;id='+ id + '"><input type="text" name="entry" value="' + text + '" class="l"></form>');
        var save_button = $('<span class="ui-icon ui-icon-disk" title="opslaan"></span>');
        var cancel_button = $('<span class="ui-icon ui-icon-close" title="annuleren"></span>');
        save_button.click(function() {
            editable_content.submit();
        });
        cancel_button.click(function() {
            $('.daglog-edit').show();
            $('.daglog-sticky').show();
            $('.daglog-review').show();
            entry.show();
            editable_content.remove();
        });
        //insert elements
        entry.hide();
        editable_content.append(save_button);
        editable_content.append(cancel_button);
        $(this).parent().append(editable_content);
    });
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
$qry = "SELECT `".$sql['table_d']."`.`id` AS `id`, `datetime`, `description`, `sticky`, `review`, `".$sql['table_users']."`.`username` AS `username`, `".$sql['table_users']."`.`id` AS `user_id`
	FROM `".$sql['database']."`.`".$sql['table_d']."`
	LEFT JOIN `".$sql['database']."`.`".$sql['table_users']."`
	ON `".$sql['table_users']."`.`id` = `".$sql['table_d']."`.`user_id_create`
    WHERE DATE(`datetime`) = '" . $date . "'
	ORDER BY `datetime` ASC, `".$sql['table_d']."`.`id` ASC";
$res = mysqli_query($sql['link'], $qry);
if (mysqli_num_rows($res)) {
	echo '<table class="grid">';
    while ($row = mysqli_fetch_assoc($res)) {
        echo '<tr><td style="vertical-align: middle; min-width: 32px;">';
        //review
        if ($row['review'] == 1) {
			echo '<a href="?s=d&amp;&amp;do=unreview&amp;id=' . $row['id'] . '" title="Niet meer markeren voor evaluatie"><span class="daglog-review ui-icon ui-icon-star"></span>';
		}
        else {
            echo '<a href="?s=d&amp;&amp;do=review&amp;id=' . $row['id'] . '" title="Markeren voor evaluatie"><span class="daglog-review ui-icon ui-icon-plus"></span>';
        }
        //sticky
		if ($row['sticky'] != 1) {
			echo '<a href="?s=d&amp;&amp;do=sticky&amp;id=' . $row['id'] . '" title="Vastmaken"><span class="daglog-sticky ui-icon ui-icon-pin-w"></span>';
		}
        echo '</td><td>';
        echo date('H:i', strtotime($row['datetime']));
        echo '</td><td class="expand"><span class="entry">';
        echo htmlspecialchars($row['description'], ENT_SUBSTITUTE);
        echo '</span>';
        //editable
        //if ($row['user_id'] == getuser()) {
            echo '<span class="daglog-edit ui-icon ui-icon-pencil" id="daglog-edit-' . $row['id'] . '" title="Bewerken"></span>';
        //}
        echo '</td><td>';
        echo ((!empty($row['username'])) ? htmlspecialchars($row['username'], ENT_SUBSTITUTE) : '');
        echo '</td></tr>';
    }
	echo '</table>';
}
else {
    echo '<p>Geen daglogging voor deze datum</p>';
}
?>