$(document).ready(function() {
	$('#descargar_datos').click(function() {
		location = ADMIN_CONTROLLER+'?task=getXML';
	});
	$('#cargar_datos').click(function() {
		$('#xml_data_file').click();
	});
});