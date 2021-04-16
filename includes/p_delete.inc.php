<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Bereikbaarheid en Verkeersmanagement, 2021
*/

if (empty($_POST)) {
	//confirmation dialog
	?>
	<h1>Verwijderen</h1>
	<p>Weet je zeker dat je werkzaamheden/evenement <?php echo htmlspecialchars($_GET['id']); ?> wil verwijderen?</p>
	<form method="post">
	<input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
	<input type="submit" value="Verwijderen"> <a href="?p=p_view&id=<?php echo htmlspecialchars($_GET['id']); ?>">Annuleren</a>
	</form>
	<?php
}
else {
	//remove action
	$qry = "DELETE FROM `".$sql['database']."`.`".$sql['table_p']."`
	WHERE `id` = '".mysqli_real_escape_string($sql['link'], $_POST['id'])."'
	LIMIT 1";
	$res = mysqli_query($sql['link'], $qry);
	if (mysqli_query($sql['link'], $qry)) $msg = 's003';
	else $msg = 'e007';

	//fix browser back button
	header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php?msg=' . $msg, TRUE, 303);
}
?>