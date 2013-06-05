$(document).ready(function() {
	$('#descargar_datos').click(function() {
		location = ADMIN_CONTROLLER+'?task=getCSV';
	});
	$('#cargar_datos').click(function() {
		$('#csv_data_file').click();
	});
});