// GENERAL

$(document).ready(function() {
	$('#asignatura').selectmenu({
		change: function() {
			var id = $('#asignatura').val();
			if (id > 0) $('#botones_top').fadeIn();
			else $('#botones_top').fadeOut();
			$('.seccion').hide();
		}
	});
});

//PREGUNTAS

$(document).ready(function() {
	$('#guardar').click(function() {
		document.getElementById('profesorForm').submit();
	});
	$('#preguntas_boton').click(function() {
		var id = parseInt($('#asignatura').val());
		if (id <= 0) {
			mensajes.alerta('Selecciona una asignatura');
			return false;
		}
		$('.seccion').hide();
		$('#cargando').show();
		$.ajax({
			url: PROFESOR_CONTROLLER,
			data: 'task=getPreguntas&asignatura='+id,
			type: 'post',
			dataType: 'json',
			success: onLoadPreguntas
		});
		return false;
	});
});

function onLoadPreguntas(data) {
	var def = !(data.length>0) || !data[0].id;
	var html = '';
	for (var i=0; i<data.length; i++) {
		html += '<div>Pregunta '+(i+1)+' <input style="width:650px" type="text" value="'+data[i].pregunta+'" name="pregunta_'+(data[i].id?data[i].id:(i+1))+'" /></div>';
	}
	$('#def').val(def ? 1 : 0);
	$('#preguntas').html(html);
	$('#preguntas input').addClass('ui-widget ui-state-default ui-corner-all');
	$('.seccion').hide();
	$('#preguntas_div').fadeIn();
}

//COMENTARIOS

var comentarios = {
	pagina:0,
	rows:0,
	buscar:''
};

$(document).ready(function() {
	$('#comentarios_boton').click(function() {
		var id = parseInt($('#asignatura').val());
		if (id <= 0) {
			mensajes.alerta('Selecciona una asignatura');
			return false;
		}
		$('#comentarios_buscar').val('');
		$('#desde').val(desde_default);
		$('#hasta').val(hasta_default);
		cargarComentarios();
		return false;
	});
	$('#pagina_anterior').click(function() {
		var start = (comentarios.pagina-2)*comentarios.rows;
		cargarComentarios('start='+start);
	});
	$('#pagina_siguiente').click(function() {
		var start = comentarios.pagina*comentarios.rows;
		cargarComentarios('start='+start);
	});
	$('#comentarios_buscar, #desde, #hasta').keyup(function(e) {
		if (e.which === 13) {
			cargarComentarios();
		}
	});
	$('#comentarios_buscar_boton').click(function() {
		cargarComentarios();
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
	var paginas = Math.ceil(numFound/comentarios.rows);
	comentarios.pagina = (start/comentarios.rows)+1;
	
	if (comentarios.pagina > 1) $('#pagina_anterior').show();
	else $('#pagina_anterior').hide();
	if (comentarios.pagina < paginas) $('#pagina_siguiente').show();
	else $('#pagina_siguiente').hide();
	
	$('#comentarios_encontrados').html(numFound);
	$('#comentarios_pagina').html(comentarios.pagina);
	$('#comentarios_paginas').html(paginas);
	
	$('#comentarios_comentarios').html('');
	var txt;
	var buscar = $('#comentarios_buscar').val();
	for (var i=0; i<len; i++) {
		txt = data.response.docs[i].comentario.toString();
		txt = colorear(txt, buscar);
		$('#comentarios_comentarios').append('<div class="comentario">'+txt+'</div>');
	}
	$('#cargando').hide();
	$('#comentarios_data').fadeIn();
}

function cargarComentarios(params) {
	$('.seccion').hide();
	$('#comentarios_clusters').hide();
	$('#comentarios_data').hide();
	$('#comentarios_div').show();
	$('#cargando').show();
	var asignatura = $('#asignatura').val();
	var desde = $('#desde').val();
	var hasta = $('#hasta').val();
	var buscar = encodeURIComponent($('#comentarios_buscar').val());
	$.ajax({
		url: PROFESOR_CONTROLLER,
		data: 'task=getComentarios&'+params+'&asignatura='+asignatura+'&desde='+desde+'&hasta='+hasta+'&buscar='+buscar,
		type: 'post',
		dataType: 'json',
		success: onLoadComentarios
	});
}

function colorear(txt, palabras) {
	if (!palabras) return txt;
	try {
		palabras = palabras.replace(/"/g, "");
		var re;
		txt = " "+txt+" ";
		
		palabras = palabras.replace(/a|á|A|Á/g, "[a|á|A|Á]");
		palabras = palabras.replace(/e|é|E|É/g, "[e|é|E|É]");
		palabras = palabras.replace(/i|í|I|Í/g, "[i|í|I|Í]");
		palabras = palabras.replace(/o|ó|O|Ó/g, "[o|ó|O|Ó]");
		palabras = palabras.replace(/u|ú|ü|U|Ú|Ü/g, "[u|ú|ü|U|Ú|Ü]");
		
		re = new RegExp("([\\W])("+palabras+")","gi");
		txt = txt.replace(re, "$1<span class=\"highlight\">$2</span>");
		palabras = palabras.split(" ");
		for (var j=0; j<palabras.length; j++) {
			if ($.inArray(palabras[j], stopWords) !== -1) continue;
			re = new RegExp("([\\W])("+palabras[j]+")","gi");
			txt = txt.replace(re, "$1<span class=\"highlight\">$2</span>");
		}
	}
	catch(e) {}
	return txt;
}

//CLUSTERING

var clusters_data;

$(document).ready(function() {
	$('#comentarios_cluster_boton').click(function() {
		$('#comentarios_data').hide();
		$('#comentarios_clusters').hide();
		$('#cargando').show();
		var asignatura = $('#asignatura').val();
		var desde = $('#desde').val();
		var hasta = $('#hasta').val();
		var buscar = encodeURIComponent($('#comentarios_buscar').val());
		$.ajax({
			url: PROFESOR_CONTROLLER,
			data: 'task=getClusters&asignatura='+asignatura+'&desde='+desde+'&hasta='+hasta+'&buscar='+buscar,
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
		clusters_data = data;
		$('#comentarios_clusters').html('');
		var html = '';
		for (var i=0; i<data.length; i++) {
			html += '<button class="cluster" onclick="cargarComentariosPorCluster(this)">'+data[i].label+'</button> ';
		}
		$('#comentarios_clusters').html(html);
	}
	$('#comentarios_clusters').fadeIn();
	$('#cargando').hide();
}

function cargarComentariosPorCluster(element) {
	var label = $(element).html();
	var ids = "";
	for (var i=0; i<clusters_data.length; i++) {
		if (clusters_data[i].label !== label) continue;
		ids = clusters_data[i].ids;
		break;
	}
	$('#comentarios_buscar').val('');
	cargarComentarios("ids="+ids.join(' '));
	$('#comentarios_buscar').val(label);
}