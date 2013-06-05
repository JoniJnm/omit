<?php

require_once(dirname(__file__).'/init/init.php');

User::getInstance(User::TYPE_ADMIN)->toLoginIfNotLoged();

?>
<!doctype html>
<html lang="es">
<head>
	<?php load('tpls.header-common'); ?>
	<title>OMIT - Administración</title>
	<script type="text/javascript" src="js/admin.js"></script>
</head>
<body>
	<div id="main">
		<?php load('tpls.mensajes'); ?>
		<div id="title">
			<a href=""><h1>OMIT - Administración</h1></a>
		</div>
		<div id="content">
			<button id="descargar_datos">Descargar datos en csv</button> <button id="cargar_datos">Subir datos en csv</button>
			<form id="csv_upload_form" method="post" action="<?php echo ADMIN_CONTROLLER; ?>" enctype="multipart/form-data">
				<input type="file" id="csv_data_file" name="csv_data" style="display:none" onchange="$('#csv_upload_form').submit()" />
				<input type="hidden" name="task" value="uploadDataCsv" />
			</form>
		</div>
		<?php load('tpls.footer'); ?>
	</div>
</body>
</html>