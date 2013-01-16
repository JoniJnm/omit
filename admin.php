<?php

require_once(dirname(__file__).'/config.php');

if (!Session::load('isAdmin')) {
	header('location: '.HTML_URL.'login.php', true, 301);
	exit;
}

$data = getData();

?>
<!doctype html>
<html lang="es">
<head>
	<title>Sistema de comentarios - Administración</title>
	<script type="text/javascript">
		var data = <?php echo json_encode($data); ?>;
	</script>
	<?php require_once(PHP_TPLS.'header-common.php'); ?>
	<script type="text/javascript" src="js/admin.js"></script>
</head>
<body>
	<div id="main">
		<?php require_once(PHP_TPLS.'mensajes.php'); ?>
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
		<?php require_once(PHP_TPLS.'footer-admin.php'); ?>
	</div>
</body>
</html>
