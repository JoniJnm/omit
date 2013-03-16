<?php

require_once(dirname(__file__).'/init/init.php');

User::getInstance('profesor')->toLoginIfNotLoged();
load('models.profesor');
$data = Profesor::getAsignaturas();

?>
<!doctype html>
<html lang="es">
<head>
	<?php load('tpls.header-common'); ?>
	<title>Sistema de comentarios - Profesor</title>
	<script type="text/javascript" src="js/profesor.js"></script>
</head>
<body>
	<div id="main">
		<?php load('tpls.mensajes'); ?>
		<div id="title">
			<h1>Sistema de comentarios - Profesor</h1>
		</div>
		<div id="content">
			<form id="profesorForm" method="post" action="<?php echo PROFESOR_CONTROLLER; ?>">
				<select style="width:250px" id="asignatura" name="asignatura">
					<option value="0">Selecciona una asignatura</option>
					<?php foreach ($data as $asignatura) : ?>
					<option value="<?php echo $asignatura->id; ?>">
						<?php echo $asignatura->nombre; ?> - <?php echo $asignatura->curso; ?>
					</option>
					<?php endforeach; ?>
				</select>
				<div id="preguntas_div" style="display:none">
					<hr />
					<div id="preguntas">

					</div>
					<input type="hidden" name="def" id="def" value="0" />
					<input type="hidden" name="task" value="guardarPreguntas" />
					<button id="guardar">Guardar</button>
				</div>
			</form>
		</div>
		<?php load('tpls.footer'); ?>
	</div>
</body>
</html>