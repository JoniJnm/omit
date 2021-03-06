<?php

/**
 * Página del profesor
 */

require_once(dirname(__file__).'/init/init.php');

User::getInstance(User::TYPE_PROFESOR)->toLoginIfNotLoged();
load('models.profesor');
load('models.uni');

$data = Profesor::getAsignaturas(User::getInstance(User::TYPE_PROFESOR)->getId()); //obtiene las asignaturas de las que es profesor
$desde = Uni::getDefaultDesde(); //obtener fecha "desde" por defecto (septiembre del curso)
$hasta = Uni::getDefaultHasta(); //obtener fecha "hasta" por defecto (septiembre del curso siguiente)

$opinion = array('neutral', 'positiva', 'negativa');
?>
<!doctype html>
<html lang="es">
<head>
	<?php load('tpls.header-common'); ?>
	<title>OMIT - Profesor</title>
	<link type="text/css" rel="stylesheet" href="css/profesor.css" />
	<script type="text/javascript" src="js/profesor.js"></script>
	<script type="text/javascript" src="js/highcharts/highcharts.js"></script>
	<script type="text/javascript" src="js/highcharts/graficos/column.js"></script>
	<script type="text/javascript">
		var desde_default = "<?php echo $desde; ?>";
		var hasta_default = "<?php echo $hasta; ?>";
		var stopWords = ["él","ésta","éstas","éste","éstos","última","últimas","último","últimos","a","añadió","aún","actualmente","adelante","además","afirmó","agregó","ahí","ahora","al","algún","algo","alguna","algunas","alguno","algunos","alrededor","ambos","ante","anterior","antes","apenas","aproximadamente","aquí","así","aseguró","aunque","ayer","bajo","bien","buen","buena","buenas","bueno","buenos","cómo","cada","casi","cerca","cierto","cinco","comentó","como","con","conocer","consideró","considera","contra","cosas","creo","cual","cuales","cualquier","cuando","cuanto","cuatro","cuenta","da","dado","dan","dar","de","debe","deben","debido","decir","dejó","del","demás","dentro","desde","después","dice","dicen","dicho","dieron","diferente","diferentes","dijeron","dijo","dio","donde","dos","durante","e","ejemplo","el","ella","ellas","ello","ellos","embargo","en","encuentra","entonces","entre","era","eran","es","esa","esas","ese","eso","esos","está","están","esta","estaba","estaban","estamos","estar","estará","estas","este","esto","estos","estoy","estuvo","ex","existe","existen","explicó","expresó","fin","fue","fuera","fueron","gran","grandes","ha","había","habían","haber","habrá","hace","hacen","hacer","hacerlo","hacia","haciendo","han","hasta","hay","haya","he","hecho","hemos","hicieron","hizo","hoy","hubo","igual","incluso","indicó","informó","junto","la","lado","las","le","les","llegó","lleva","llevar","lo","los","luego","lugar","más","manera","manifestó","mayor","me","mediante","mejor","mencionó","menos","mi","mientras","misma","mismas","mismo","mismos","momento","mucha","muchas","mucho","muchos","muy","nada","nadie","ni","ningún","ninguna","ningunas","ninguno","ningunos","no","nos","nosotras","nosotros","nuestra","nuestras","nuestro","nuestros","nueva","nuevas","nuevo","nuevos","nunca","o","ocho","otra","otras","otro","otros","para","parece","parte","partir","pasada","pasado","pero","pesar","poca","pocas","poco","pocos","podemos","podrá","podrán","podría","podrían","poner","por","porque","posible","próximo","próximos","primer","primera","primero","primeros","principalmente","propia","propias","propio","propios","pudo","pueda","puede","pueden","pues","qué","que","quedó","queremos","quién","quien","quienes","quiere","realizó","realizado","realizar","respecto","sí","sólo","se","señaló","sea","sean","según","segunda","segundo","seis","ser","será","serán","sería","si","sido","siempre","siendo","siete","sigue","siguiente","sin","sino","sobre","sola","solamente","solas","solo","solos","son","su","sus","tal","también","tampoco","tan","tanto","tenía","tendrá","tendrán","tenemos","tener","tenga","tengo","tenido","tercera","tiene","tienen","toda","todas","todavía","todo","todos","total","tras","trata","través","tres","tuvo","un","una","unas","uno","unos","usted","va","vamos","van","varias","varios","veces","ver","vez","y","ya","yo"];
	</script>
	<script type="text/javascript">
		var OPINION = ["<?php echo implode('","', $opinion); ?>"];
	</script>
</head>
<body>
	<div id="main">
		<?php load('tpls.mensajes'); ?>
		<div id="title">
			<a href=""><h1>OMIT - Profesor</h1></a>
		</div>
		<div id="content">
			<form id="csv_upload_form" method="post" action="<?php echo PROFESOR_CONTROLLER; ?>" enctype="multipart/form-data">
				<input type="file" id="csv_data_file" name="csv_file" style="display:none" onchange="$('#csv_upload_form').submit()" />
				<input type="hidden" name="asignatura" id="csv_upload_asignatura" />
				<input type="hidden" name="task" value="uploadDataCsv" />
			</form>
			<form id="profesorForm" method="post" action="<?php echo PROFESOR_CONTROLLER; ?>">
				<select style="width:250px" id="asignatura" name="asignatura">
					<option value="0">Selecciona una asignatura</option>
					<?php foreach ($data as $asignatura) : ?>
					<option value="<?php echo $asignatura->id; ?>">
						<?php echo $asignatura->nombre; ?> - <?php echo $asignatura->curso; ?>
					</option>
					<?php endforeach; ?>
				</select>
				<span id="botones_top" style="display:none">
					<button id="preguntas_boton">Preguntas</button>
					<button id="comentarios_boton">Comentarios</button>
					<button id="cargar_alumnos_boton">Cargar alumnos</button>
				</span>
				<hr />
				<div class="seccion" id="preguntas_div" style="display:none">
					<div id="preguntas_estadisticas_parent">
						<button id="preguntas_estadisticas_grafico_anterior">Mes anterior</button>
						<button id="preguntas_estadisticas_grafico_siguiente">Mes Siguiente</button>
						<div id="preguntas_estadisticas_grafico"></div>
					</div>
					<div id="preguntas">

					</div>
					<br />
					<div>
						<input type="hidden" name="def" id="def" value="0" />
						<input type="hidden" name="task" value="guardarPreguntas" />
						<button id="preguntas_editar">Editar</button>
						<button id="preguntas_estadisticas">Estadísticas</button>
						<span id="preguntas_guardar_parent" style="display:none">				
							<button id="preguntas_guardar">Guardar</button>
							<span style="color:red">Se perderán las valoraciones actuales si se modifican las preguntas</span>
						</span>
					</div>
					<div id="preguntas_confirmacion" title="¿Desea continuar?" style="display:none">
						<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Se perderán las valoraciones actuales si se modifican las preguntas</p>
					</div>
				</div>
			</form>
			<div class="seccion" id="comentarios_div" style="display:none">
				<div> 
					<img src="imagenes/lupa.png" alt="lupa" />
					<input type="text" placeholder="Buscar..." id="comentarios_buscar" name="comentarios_buscar" />
					Fecha <input class="date" type="text" name="desde" id="desde" />
					- <input class="date" type="text" name="hasta" id="hasta" />
					Opinión <select id="comentarios_opinion" name="comentarios_opinion" style="width:144px">
						<option value="-1">Todas</option>
					</select>
					<br />
					<button id="comentarios_buscar_boton">Buscar comentarios</button>
				</div>
				<br />
				<div id="comentarios_data" style="display:none">
					<div id="comentarios_info">
						<div>
							Mostrando página <span id="comentarios_pagina"></span> de <span id="comentarios_paginas"></span>
							- <span id="comentarios_encontrados"></span> comentarios totales
						</div>
						<div>
							<button id="comentarios_cluster_boton">Buscar temas</button>
							<button id="comentarios_estadisticas_boton">Estadísticas</button>
							<img id="comentarios_estadisticas_ajax_img" style="vertical-align:middle;display:none" src="imagenes/ajax.gif" alt="" />
						</div>
					</div>
					<div id="comentarios_estadisticas" class="comentario">
						<div style="font-weight:bold">Estadísticas</div>
						<?php foreach ($opinion as $tmp) : ?>
						<div>
							Opinión <span class="<?php echo $tmp; ?>"><?php echo ucfirst($tmp); ?></span>: 
							<span id="opinion_<?php echo $tmp; ?>"></span>
						</div>
						<?php endforeach; ?>
					</div>
					<div id="comentarios_comentarios"></div>
					<div id="comentarios_pagination">
						<div style="float:left;width:50%">
							<button id="pagina_anterior">Página anterior</button>
						</div>
						<div style="float:right;width:50%;text-align:right">
							<button id="pagina_siguiente">Siguiente página</button>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div id="comentarios_clusters" style="display:none">
					
				</div>
			</div>
			<div class="seccion" id="cargando" style="display:none;font-weight:bold">
				Cargando... <img style="vertical-align:middle" src="imagenes/ajax.gif" alt="" />
			</div>
		</div>
		<?php load('tpls.footer'); ?>
	</div>	
</body>
</html>