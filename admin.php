<?php

require_once(dirname(__file__).'/config.php');

if (!Session::get('isAdmin')) {
	header('location: '.HTML_URL.'login.php?go=admin', true, 301);
	exit;
}

?>
<!doctype html>
<html lang="es">
<head>
	<?php load('tpls.header-common'); ?>
	<title>Sistema de comentarios - Administración</title>
	<script type="text/javascript" src="js/admin.js"></script>
</head>
<body>
	<div id="main">
		<?php load('tpls.mensajes'); ?>
		<div id="title">
			<h1>Sistema de comentarios - Administración</h1>
		</div>
		<div id="content">
			<button id="descargar_datos">Descargar datos en xml</button> <button id="cargar_datos">Subir datos en xml</button>
			<form id="xml_upload_form" method="post" action="<?php echo ADMIN_CONTROLLER; ?>" enctype="multipart/form-data">
				<input type="file" id="xml_data_file" name="xml_data" style="display:none" onchange="$('#xml_upload_form').submit()" />
				<input type="hidden" name="task" value="uploadDataXml" />
			</form>
		</div>
		<?php load('tpls.footer-admin'); ?>
	</div>
</body>
</html>
