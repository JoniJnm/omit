<?php

require_once(dirname(__file__).'/config.php');

User::isLoged('alumno') or User::toLogin('alumno');

load('models.uni');
load('models.preguntas');
$data = Uni::getData();
$preguntas = Preguntas::getDefault();
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
	<title>Sistemas de comentarios de asignaturas</title>
	<script type="text/javascript" src="js/alumno.js"></script>
</head>
<body>
	<div id="main">
		<?php load('tpls.mensajes'); ?>
		<div id="title">
			<h1>Sistema de comentarios de asignaturas</h1>
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
				<div id="preguntas">
					<table>
						<?php $i=0;foreach ($preguntas as $p) : ?>
						<tr>
							<td class="pregunta" colspan="2"><?php echo (++$i).". ".$p->pregunta; ?></td>
						</tr>
						<tr>
							<td class="satisfaccion_td">
								<input type="hidden" data-id="<?php echo $p->id; ?>" name="respuesta_<?php echo $p->id; ?>" id="respuesta_<?php echo $p->id; ?>" />
								<span id="satisfaccion_<?php echo $p->id; ?>"></span>
							</td>
							<td class="satisfaccion_slider_td">
								<div data-id="<?php echo $p->id; ?>" class="satisfaccion_slider" style="width:200px"></div>
							</td>
						</tr>
						<?php endforeach; ?>
					</table>
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
