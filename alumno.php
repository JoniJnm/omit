<?php

/**
 * Página de alumno
 */

require_once(dirname(__file__).'/init/init.php');

User::getInstance(User::TYPE_ALUMNO)->toLoginIfNotLoged();

load('models.alumno');
$data = Alumno::getUniData(User::getInstance(User::TYPE_ALUMNO)->getId()); //obtiene: titulaciones, cursos, asignaturas y profesores asociados al alumno
?>

<!doctype html>
<html lang="es">
<head>
	<script type="text/javascript">
		/*<![CDATA[*/
		var data = <?php echo json_encode($data); ?>;
		/*]]>*/
	</script>
	<?php load('tpls.header-common'); ?>
	<title>OMIT - Alumno</title>
	<script type="text/javascript" src="js/alumno.js"></script>
</head>
<body>
	<div id="main">
		<?php load('tpls.mensajes'); ?>
		<div id="title">
			<a href=""><h1>OMIT - Alumno</h1></a>
		</div>
		<div id="content">
			<div id="parte1" style="display:none">
				<h2>Selecciona el profesor que quieres valorar.</h2>
				<table class="tabla_horizontal">
					<tr>
						<td>Selecciona titulación</td>
						<td>
							<select name="titulacion" id="titulacion">
								<option value="0">Selecciona titulación</option>
							</select>
						</td>
						<td>Selecciona curso</td>
						<td>
							<select name="curso" id="curso">
								<option value="0">Selecciona titulación</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Selecciona asignatura</td>
						<td>
							<select name="asignatura" id="asignatura">
								<option value="0">Selecciona curso</option>
							</select>
						</td>
						<td>Selecciona profesor</td>
						<td>
							<select name="profesor" id="profesor">
								<option value="0">Selecciona asignatura</option>
							</select>
						</td>
					</tr>
				</table>
			</div>
			<div id="parte2" style="display:none">
				<h2>Marca tu valoración para cada una de las siguiente preguntas:</h2>
				<div>
					Valorar preguntas
					<span class="radio" id="preguntasRadio">
						<input type="radio" id="preguntasSi" name="preguntas" /><label for="preguntasSi">Sí</label>
						<input type="radio" id="preguntasNo" name="preguntas" /><label for="preguntasNo">No</label>
					</span>
				</div>
				<div id="preguntas">
					
				</div>
				<div id="cargando" style="font-weight:bold">
					Cargando... <img style="vertical-align:middle" src="imagenes/ajax.gif" alt="" />
				</div>
			</div>
			<div id="parte3" style="display:none">
				<h2>Puedes incluir un comentario sobre cualquier aspecto relacionado con las asignatura o el profesor.</h2>
				<table class="tabla_horizontal">
					<tr>
						<td style="vertical-align:top">Comentario</td>
						<td><textarea id="comentario" placeholder="Puedes hablar de lo que quieras en el comentario." style="width:550px; height:200px"></textarea></td>
					</tr>
				</table>
				<br />
				<input type="hidden" name="def" id="def" value="0" />
				<button id="enviar">Enviar</button>
			</div>
			<div>
				<br />
				<table width="100%">
					<tr>
						<td><button id="anterior">&lt;&lt; Anterior</button></td>
						<td style="text-align:right"><button id="siguiente">Siguiente &gt;&gt;</button></td>
					</tr>
				</table>
			</div>
		</div>
		<?php load('tpls.footer'); ?>
	</div>
</body>
</html>
