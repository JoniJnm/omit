$(document).ready(function() {
	$('#descargar_datos').click(function() {
		location = ADMIN_CONTROLLER+'?task=getCSV';
	});
	$('#cargar_datos').click(function() {
		$('#csv_data_file').click();
	});
	$('#borrar_sistema').click(function() {
		$("#borrar_sistema_confirmacion").show();
		$("#borrar_sistema_confirmacion").dialog({
			resizable: false,
			height: 270,
			width: 430,
			modal: true,
			buttons: {
				"Continuar": function() {
					$(this).dialog("close");
					document.getElementById('borrar_sistema_form').submit();
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			}
		});
	});
});