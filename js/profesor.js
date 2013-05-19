// GENERAL

$(document).ready(function() {
	$('#asignatura').selectmenu({
		change: function() {
			var id = $('#asignatura').val();
			if (id > 0)
				$('#botones_top').fadeIn();
			else
				$('#botones_top').fadeOut();
			$('.seccion').hide();
		}
	});
});

//PREGUNTAS

$(document).ready(function() {
	$('#preguntas_boton').click(function() {
		$('#preguntas_estadisticas_parent').hide();
		$('#preguntas_estadisticas').show();
		var id = parseInt($('#asignatura').val());
		if (id <= 0) {
			mensajes.alerta('Selecciona una asignatura');
			return false;
		}
		$('.seccion').hide();
		$('#cargando').show();
		$.ajax({
			url: PROFESOR_CONTROLLER,
			data: 'task=getPreguntas&asignatura=' + id,
			type: 'post',
			dataType: 'json',
			success: onLoadPreguntas
		});
		return false;
	});
	$('#preguntas_editar').click(function() {
		$('span.pregunta').hide();
		$('input.pregunta').show();
		$('#preguntas_editar').hide();
		$('#preguntas_parent').show();
		return false;
	});
	$('#preguntas_guardar').click(function() {
		$("#preguntas_confirmacion").show();
		$("#preguntas_confirmacion").dialog({
			resizable: false,
			height: 225,
			width: 400,
			modal: true,
			buttons: {
				"Continuar": function() {
					$(this).dialog("close");
					document.getElementById('profesorForm').submit();
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			}
		});
		return false;
	});
});

function onLoadPreguntas(data) {
	var def = !(data.length > 0) || !data[0].id;
	var len = data.length;
	var html = '<table>';
	for (var i = 0; i < 10; i++) {
		html += '<tr><td class="pregunta">Pregunta ' + (i + 1) + '</td><td><span class="pregunta">' + (i < len ? data[i].pregunta : '-') + '</span><input class="pregunta" style="display:none" type="text" value="' + (i < len ? data[i].pregunta : '') + '" name="pregunta_' + (i < len && data[i].id ? data[i].id : (i + 1)) + '" /></td></tr>';
	}
	html += '</table>';
	$('#def').val(def ? 1 : 0);
	$('#preguntas').html(html);
	$('#preguntas input').addClass('ui-widget ui-state-default ui-corner-all');
	$('.seccion').hide();
	$('#preguntas_div').fadeIn();
}

//COMENTARIOS

var comentarios = {
	pagina: 0,
	rows: 0,
	buscar: ''
};

$(document).ready(function() {
	$('#comentarios_boton').click(function() {
		var id = parseInt($('#asignatura').val());
		if (id <= 0) {
			mensajes.alerta('Selecciona una asignatura');
			return false;
		}
		$('#comentarios_estadisticas').hide();
		$('#comentarios_buscar').val('');
		$('#desde').val(desde_default);
		$('#hasta').val(hasta_default);
		cargarComentarios();
		return false;
	});
	$('#pagina_anterior').click(function() {
		var start = (comentarios.pagina - 2) * comentarios.rows;
		cargarComentarios('start=' + start);
	});
	$('#pagina_siguiente').click(function() {
		var start = comentarios.pagina * comentarios.rows;
		cargarComentarios('start=' + start);
	});
	$('#comentarios_buscar, #desde, #hasta').keyup(function(e) {
		if (e.which === 13) {
			cargarComentarios();
		}
	});
	$('#comentarios_buscar_boton').click(function() {
		cargarComentarios();
	});
	for (var i = 0; i < OPINION.length; i++) {
		select.addOption('#comentarios_opinion', ucfirst(OPINION[i]), i);
	}
	$('#comentarios_opinion').selectmenu();
	$('#comentarios_estadisticas_boton').click(function() {
		if ($('#comentarios_estadisticas').css('display') == 'none')
			cargarOpiniones();
		return false;
	});
});

function onLoadComentarios(data) {
	if (!data || !data.response || !data.response.numFound) {
		//error en la búsqueda (bad request?)
		$('#comentarios_comentarios').html('');
		$('#comentarios_encontrados').html(0);
		$('#comentarios_pagina').html(1);
		$('#comentarios_paginas').html(1);
		$('#cargando').hide();
		$('#pagina_anterior').hide();
		$('#pagina_siguiente').hide();
		$('#comentarios_data').fadeIn();
		return;
	}
	var start = data.response.start;
	var numFound = data.response.numFound;
	comentarios.rows = data.responseHeader.params.rows;
	var len = data.response.docs.length;
	var paginas = Math.ceil(numFound / comentarios.rows);
	comentarios.pagina = (start / comentarios.rows) + 1;

	if (comentarios.pagina > 1)
		$('#pagina_anterior').show();
	else
		$('#pagina_anterior').hide();
	if (comentarios.pagina < paginas)
		$('#pagina_siguiente').show();
	else
		$('#pagina_siguiente').hide();

	$('#comentarios_encontrados').html(numFound);
	$('#comentarios_pagina').html(comentarios.pagina);
	$('#comentarios_paginas').html(paginas);

	$('#comentarios_comentarios').html('');
	var txt, extra, doc;
	var buscar = $('#comentarios_buscar').val();
	for (var i = 0; i < len; i++) {
		doc = data.response.docs[i];
		txt = doc.comentario.toString();
		txt = colorear(txt, buscar);
		extra = '<div class="opinion"><span class="title">Opinión</span>: ';
		extra += '<span class="' + OPINION[doc.opinion] + '">' + OPINION[doc.opinion] + '</span>';
		extra += '</div>';

		extra = '<div class="comentario_extra">' + extra + '</div>';
		$('#comentarios_comentarios').append('<div class="comentario">' + txt + extra + '</div>');
	}
	$('#cargando').hide();
	$('#comentarios_data').fadeIn();
}

function cargarComentariosParams() {
	var asignatura = $('#asignatura').val();
	var desde = $('#desde').val();
	var hasta = $('#hasta').val();
	var buscar = encodeURIComponent($('#comentarios_buscar').val());
	var opinion = $('#comentarios_opinion').val();
	return 'asignatura=' + asignatura + '&desde=' + desde + '&hasta=' + hasta + '&buscar=' + buscar + '&opinion=' + opinion
}
function cargarComentarios(params) {
	if (!params) params = '';
	$('.seccion').hide();
	$('#comentarios_estadisticas').hide();
	$('#comentarios_clusters').hide();
	$('#comentarios_data').hide();
	$('#comentarios_div').show();
	$('#cargando').show();
	$.ajax({
		url: PROFESOR_CONTROLLER,
		data: 'task=getComentarios&'+params+'&'+cargarComentariosParams(),
		type: 'post',
		dataType: 'json',
		success: onLoadComentarios
	});
}

function cargarOpiniones() {
	$('#comentarios_estadisticas_ajax_img').show();
	$.ajax({
		url: PROFESOR_CONTROLLER,
		data: 'task=getComentarios&'+cargarComentariosParams(),
		type: 'post',
		dataType: 'json',
		success: onLoadOpiniones
	});
}

function onLoadOpiniones(data) {
	$('#comentarios_estadisticas_ajax_img').hide();
	var info = [];
	for (var i=0; i<OPINION.length; i++) {
		info[i] = 0;
	}
	for (var i=0; i<data.response.docs.length; i++) {
		info[data.response.docs[i].opinion]++;
	}
	for (var i=0; i<OPINION.length; i++) {
		$('#opinion_'+OPINION[i]).html(info[i]);
	}
	$('#comentarios_estadisticas').fadeIn();
}

function colorear(txt, palabras) {
	if (!palabras)
		return txt;
	try {
		palabras = palabras.replace(/"/g, "");
		var re;
		txt = " " + txt + " ";
		var palabras_regrex = colorear_regrex(palabras);
		re = new RegExp("([\\W])(" + palabras_regrex + ")", "gi");
		txt = txt.replace(re, "$1<span class=\"highlight\">$2</span>");
		palabras = palabras.split(" ");
		palabras_regrex = palabras_regrex.split(" ");
		for (var j = 0; j < palabras.length; j++) {
			if ($.inArray(palabras[j], stopWords) !== -1)
				continue;
			re = new RegExp("([\\W])(" + palabras_regrex[j] + ")", "gi");
			txt = txt.replace(re, "$1<span class=\"highlight\">$2</span>");
		}
	}
	catch (e) {
	}
	return txt;
}

function colorear_regrex(str) {
	str = str.replace(/a|á|A|Á/g, "[a|á|A|Á]");
	str = str.replace(/e|é|E|É/g, "[e|é|E|É]");
	str = str.replace(/i|í|I|Í/g, "[i|í|I|Í]");
	str = str.replace(/o|ó|O|Ó/g, "[o|ó|O|Ó]");
	str = str.replace(/u|ú|ü|U|Ú|Ü/g, "[u|ú|ü|U|Ú|Ü]");
	return str;
}

//CLUSTERING

$(document).ready(function() {
	$('#comentarios_cluster_boton').click(function() {
		$('#comentarios_data').hide();
		$('#comentarios_clusters').hide();
		$('#cargando').show();
		var asignatura = $('#asignatura').val();
		var desde = $('#desde').val();
		var hasta = $('#hasta').val();
		var buscar = encodeURIComponent($('#comentarios_buscar').val());
		var opinion = $('#comentarios_opinion').val();
		$.ajax({
			url: PROFESOR_CONTROLLER,
			data: 'task=getClusters&asignatura=' + asignatura + '&desde=' + desde + '&hasta=' + hasta + '&buscar=' + buscar + '&opinion=' + opinion,
			type: 'post',
			dataType: 'json',
			success: onLoadClusters
		});
	});
});

function onLoadClusters(data) {
	if (parseInt(data.length) === 0) {
		$('#comentarios_clusters').html('No se han encontrado comentarios sobre los parámetros de búsqueda.');
	}
	else {
		$('#comentarios_clusters').html('');
		var html = '';
		for (var i = 0; i < data.length; i++) {
			html += '<button class="cluster" onclick="cargarComentariosPorCluster(this)">' + data[i].label + '</button> ';
		}
		$('#comentarios_clusters').html(html);
	}
	$('#comentarios_clusters').fadeIn();
	$('#cargando').hide();
}

function cargarComentariosPorCluster(element) {
	var label = $(element).html();
	$('#comentarios_buscar').val(label)
	cargarComentarios();
}


// Estadísticas (preguntas)
var preg_std;
var preg_std_i;

$(document).ready(function() {
	$('#preguntas_estadisticas').click(function() {
		$('#cargando').show();
		$('#preguntas_estadisticas').hide();
		cargarGrafico();
		return false;
	});
	$('#preguntas_estadisticas_grafico_anterior').click(function() {
		preg_std_i--;
		cargarGraficoData();
		return false;
	});
	$('#preguntas_estadisticas_grafico_siguiente').click(function() {
		preg_std_i++;
		cargarGraficoData();
		return false;
	});
});

function cargarGrafico() {
	var asignatura = $('#asignatura').val();
	$.ajax({
		url: PROFESOR_CONTROLLER,
		data: 'task=getRespuestas&asignatura=' + asignatura,
		type: 'post',
		dataType: 'json',
		success: onLoadRespuestas
	});
}

function onLoadRespuestas(data) {
	$('#cargando').hide();
	$('#preguntas_estadisticas_parent').show();
	preg_std_i = 0;
	preg_std = data;
	cargarGraficoData();
}

function cargarGraficoData() {
	if (preg_std_i >= preg_std.length) preg_std_i = 0;
	if (preg_std_i < 0) preg_std_i = preg_std.length-1;
	mostrarGrafico('Valoraciones de los usuarios', '', preg_std[preg_std_i].mes, 'Valoración', preg_std[preg_std_i].series);
}

function mostrarGrafico(titulo, subtitulo, categorias, ejey, datos) {
	var g = column_grafic;
	g.title.text = titulo;
	g.subtitle.text = subtitulo;
	g.xAxis.categories = categorias;
	g.yAxis.title.text = ejey;
	g.series = datos;
	$('#preguntas_estadisticas_grafico').highcharts(g);
}