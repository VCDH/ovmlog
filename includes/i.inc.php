<?php
/*
 * Gemeente Den Haag, Dienst Stadsbeheer, Afdeling Verkeersmanagement en Openbare Verlichting, 2013
*/

//default values
$title = 'Incident toevoegen';
$date = date('d-m-Y');
$time = date('H:i');
?>

<h1><?php echo $title; ?></h1>

<form action="?s=i" method="post">

<table>
<tr>
	<td><label for="date">datum:</label></td>
	<td><input class="s" name="date" id="date" type="text" value="<?php echo $date; ?>" /></td>
	<td><label for="scenario">scenario:</label></td>
	<td><input class="m" name="scenario" id="scenario" type="text" value="" /> (laat leeg in geval van maatwerk)</td>
</tr>
<tr>
	<td><label for="road">wegnr:</label></td>
	<td><input class="s" name="road" id="road" type="text" value="" /></td>
	<td><label for="location">locatie:</label></td>
	<td><input class="m" name="location" id="location" type="text" value="" /></td>
</tr>
</table>

<table>
<tr>
	<td>&nbsp;</td>
	<td><label for="time">tijd</label></td>
	<td><label for="description">beschrijving</label></td>
	<td><label for="contact">gesproken met</label></td>
</tr>
<tr>
	<td class="count">1</td>
	<td><input class="s" name="time" id="time" type="text" value="<?php echo $time; ?>" /></td>
	<td><textarea name="description" id="description" rows="4" cols="40"></textarea></td>
	<td><input class="m" name="contact" id="contact" type="text" value="" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td colspan="3"><input type="checkbox" name="closed" id="closed" value="true" /><label for="closed">melding afsluiten</label></td>
</tr>
</table>

<p><input type="submit" name"add" value="nog een tijdstip toevoegen"> <input type="submit" name"save" value="opslaan en naar overzicht"></p>

</form>