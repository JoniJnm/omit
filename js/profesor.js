var página = 0;
var rows = 0;

$(document).ready(function() {
	$('#asignatura').selectmenu({
		change: function() {
			$('.seccion').hide();
		}
	});
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
			data: 'task=getAsignaturas&asignatura='+id,
			type: 'post',
			dataType: 'json',
			success: onLoadAsignaturas
		});
		return false;
	});
	$('#comentarios_boton').click(function() {
		var id = parseInt($('#asignatura').val());
		if (id <= 0) {
			mensajes.alerta('Selecciona una asignatura');
			return false;
		}
		cargarComentarios('asignatura='+id);
		return false;
	});
	$('#pagina_anterior').click(function() {
		var start = (pagina-2)*rows;
		cargarComentarios('asignatura='+$('#asignatura').val()+'&start='+start);
	});
	$('#pagina_siguiente').click(function() {
		var start = pagina*rows;
		cargarComentarios('asignatura='+$('#asignatura').val()+'&start='+start);
	});
});

function cargarComentarios(params) {
	$('.seccion').hide();
	$('#cargando').show();
	$.ajax({
		url: PROFESOR_CONTROLLER,
		data: 'task=getComentarios&'+params,
		type: 'post',
		dataType: 'json',
		success: onLoadComentarios
	});
}

function onLoadAsignaturas(data) {
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

function onLoadComentarios(data) {
	var start = data.response.start;
	var numFound = data.response.numFound;
	rows = data.responseHeader.params.rows;
	var len = data.response.docs.length;
	var doc;
	var paginas = Math.ceil(numFound/rows);
	pagina = (start/rows)+1;
	
	if (pagina > 1) $('#pagina_anterior').show();
	else $('#pagina_anterior').hide();
	if (pagina < paginas) $('#pagina_siguiente').show();
	else $('#pagina_siguiente').hide();
	
	
	$('#comentarios_encontrados').html(numFound);
	$('#comentarios_pagina').html(pagina);
	$('#comentarios_paginas').html(paginas);
	
	$('#comentarios_comentarios').html('');
	for (var i=0; i<len; i++) {
		doc = data.response.docs[i];
		$('#comentarios_comentarios').append('<div class="comentario">'+doc.comentario+'</div>');
	}
	$('.seccion').hide();
	$('#comentarios_div').fadeIn();
}