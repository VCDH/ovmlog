/*
 	scenariobrowser - viewer en editor voor verkeersmanagementscenario's
    Copyright (C) 2016-2019 Gemeente Den Haag, Netherlands
    Developed by Jasper Vries
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/
var clear_upload_timeout;

$(function () {
	'use strict';
	// Change this to the location of your server-side upload handler:
	$('#fileupload').fileupload({
		url: 'bijlage.php?do=upload&id=' + $('input[name=id]').val(),
		dataType: 'json',
		start: function() {
			clearTimeout(clear_upload_timeout);
		},
		done: function (e, data) {
			$.each(data.result.files, function (index, file) {
				if (file.error) {
					$('<p/>').text('Kan ' + file.name + ' niet uploaden: ' + file.error).appendTo('#files');
				}
				else {
					$('<p/>').text(file.name + ' toegevoegd').appendTo('#files');
				}
			});
			clear_upload_timeout = setTimeout(clear_upload_result, 5000);
			load_attachments();
		},
		fail: function (e, data) {
			$('<p/>').text('Kan bestand niet uploaden: ' + data.errorThrown).appendTo('#files');
			clear_upload_timeout = setTimeout(clear_upload_result, 5000);
		}
	}).prop('disabled', !$.support.fileInput)
		.parent().addClass($.support.fileInput ? undefined : 'disabled');
});

function clear_upload_result() {
	$('#files').empty();
}

function load_attachments() {
	$.getJSON('bijlage.php', { do: 'getlist', id: $('input[name=id]').val() })
	.done (function(json) {
		$('#bijlagen').empty();
		$.each(json, function (i, data) {
			$('#bijlagen').append('<li>' + ((data.toegang == 0) ? '<span class="ui-icon ui-icon-locked" title="Bijlage alleen toegankelijk voor eigen organisatie"></span>' : ((data.toegang == 1) ? '<span class="ui-icon ui-icon-contact" title="Bijlage alleen toegankelijk na inloggen"></span>' : '')) + ((data.archief == 1) ? '<span class="ui-icon ui-icon-suitcase" title="Bijlage is gearchiveerd"></span>' : '') + '<a href="bijlage.php?do=getfile&amp;id=' + data.id + '">' + data.bestandsnaam + '</a> (' + data.grootte + ')<span class="ui-icon ui-icon-trash" title="Verwijderen" rel="' + data.id + '"></span> <br>Toegevoegd door ' + data.user + ' op ' + data.datum + '</li>');
		});
		//delete
		$('span.ui-icon-trash').click( function() {
			var id = $(this).attr('rel');
			if ($('#confirmwindow').length == 0) {
				$('html').append('<div id="confirmwindow"></div>');
			}
			$('#confirmwindow').html('Bijlage verwijderen?');
			$('#confirmwindow').dialog({
				title: 'Bevestiging',
				height: 'auto',
				width: 400,
				modal: true,
				position: { my: "center bottom", at: "center", of: window },
				buttons: [
					{
						text: "OK",
						click: function() {
							$.get('bijlage.php', { do: 'delete', id: id })
							.done( function() {
								$('#confirmwindow').dialog( "close" );
								load_attachments();
							});
						}
					},
					{
						text: "Annuleren",
						click: function() {
							$( this ).dialog( "close" );
						}
					}
				]
			});
		});
	});
}

$( document ).ready(function() {
	load_attachments();
});