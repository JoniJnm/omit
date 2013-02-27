<?php

require_once(dirname(__file__).'/config.php');

User::isLoged('profesor') or User::toLogin('profesor');

?>
<!doctype html>
<html lang="es">
<head>
	<?php load('tpls.header-common'); ?>
	<title>Sistema de comentarios - Profesor</title>
</head>
<body>
	<div id="main">
		<?php load('tpls.mensajes'); ?>
		<div id="title">
			<h1>Sistema de comentarios - Profesor</h1>
		</div>
		<div id="content">
			En construcción...
		</div>
		<?php load('tpls.footer'); ?>
	</div>
</body>
</html>