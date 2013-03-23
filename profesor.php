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
	<link type="text/css" rel="stylesheet" href="css/profesor.css" />
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
				<button id="preguntas_boton">Mostrar preguntas</button>
				<button id="comentarios_boton">Mostrar comentarios</button>
				<hr />
				<div class="seccion" id="preguntas_div" style="display:none">
					<div id="preguntas">

					</div>
					<input type="hidden" name="def" id="def" value="0" />
					<input type="hidden" name="task" value="guardarPreguntas" />
					<button id="guardar">Guardar</button>
				</div>
			</form>
			<div class="seccion" id="comentarios_div" style="display:none">
				<div id="comentarios_info">
					Mostrando página <span id="comentarios_pagina"></span> de <span id="comentarios_paginas"></span>
					- <span id="comentarios_encontrados"></span> comentarios totales
				</div>
				<div id="comentarios_comentarios"></div>
				<div id="comentarios_pagination">
					<div style="float:left;width:50%">
						<button id="pagina_anterior">Página anterior</button>
					</div>
					<div style="float:right;width:50%;text-align:right">
						<button id="pagina_siguiente">Siguiente página</button>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			<div class="seccion" id="cargando" style="display:none;font-weight:bold">
				Cargando... <img style="vertical-align:middle" src="imagenes/ajax.gif" alt="" />
			</div>
		</div>
		<?php load('tpls.footer'); ?>
	</div>
</body>
</html>