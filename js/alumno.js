var parte = 0;
var preguntas_cargadas = false;

function irParte(from, to) {
	if ($('#parte'+to).length == 0) return;
	var asignatura = $('#asignatura').val();
	var profesor = $('#profesor').val();
	if (from == 1) {
		if (asignatura <= 0) {
			mensajes.alerta("Seleccione una asignatura");
			return;
		}
		else if (profesor <= 0) {
			mensajes.alerta("Seleccione un profesor");
			return;
		}
		mensajes.borrar();
	}
	
	parte = to;
	if ($('#parte'+from).length == 0) {
		$('#parte'+to).show();
	}
	else {
		$('#parte'+from).fadeOut('slow', function() {
			$('#parte'+to).fadeIn();
		});
	}
	if ($('#parte'+(to+1)).length == 0) $('#siguiente').hide();
	else $('#siguiente').show();
	
	if ($('#parte'+(to-1)).length == 0) $('#anterior').hide();
	else $('#anterior').show();
	
	if (from == 1 && to == 2) {
		$('#preguntas').hide();
		$('#cargando').hide();
		preguntas_cargadas = false;
		$('#preguntasSi')[0].checked = false;
		$('#preguntasNo')[0].checked = true;
		$('#preguntasRadio').buttonset('refresh');
	}
}

$(document).ready(function() {
	$('#preguntasSi').next().click(function() {
		if (preguntas_cargadas) {
			$('#preguntas').show();
		}
		else {
			preguntas_cargadas = true;
			$('#cargando').show();
			var asignatura = $('#asignatura').val();
			var profesor = $('#profesor').val();
			$.ajax({
				url: ALUMNO_CONTROLLER,
				method: 'post',
				data: 'task=getPreguntas&asignatura='+asignatura+'&profesor='+profesor,
				dataType: 'json',
				success: cargarPreguntas
			});
		}
	});
	$('#preguntasNo').next().click(function() {
		$('#preguntas').hide();
		$('#cargando').hide();
	});
});

$(document).ready(function() {
	irParte(0, 1);
	$("#siguiente").click(function() {
		irParte(parte, parte+1);
	});
	$("#anterior").click(function() {
		irParte(parte, parte-1);
	});
});

$(document).ready(function() {
	$('#enviar').click(function() {
		var asignatura = $('#asignatura').val();
		var profesor = $('#profesor').val();
		var comentario = $('#comentario').val();
		var def = $('#def').val();
		if (asignatura <= 0) {
			mensajes.alerta("Seleccione una asignatura");
		}
		else if (profesor <= 0) {
			mensajes.alerta("Seleccione un profesor");
		}
		else if (comentario.length == 0) {
			mensajes.alerta("Introduzca un comentario");
		}
		else {
			mensajes.borrar();
			var respuestas = [];
			if ($('#preguntasSi')[0].checked) {
				$('.satisfaccion_td input').each(function(i, e) {
					var id = $(e).attr('data-id');
					var v = $(e).val();
					respuestas.push(id+":"+v);
				});
			}
			respuestas = respuestas.join(';');
			$.ajax({
				url: ALUMNO_CONTROLLER,
				type: 'POST',
				dataType: 'test',
				data: "task=insertarComentario&asignatura="+asignatura+"&profesor="+profesor+"&comentario="+encodeURIComponent(comentario)+"&respuestas="+respuestas+'&def='+def,
				complete: function(data, textStatus, jqXHR ) {
					if (data.responseText == "OK") {
						mensajes.info("Comentario añadido");
						select.refresh('#profesor', 0);
						irParte(parte, 1);
						$('#comentario').val('');
						$(".satisfaccion_slider").each(function(i, e) {
							$(e).slider('value', 3);
						});
					}
					else {
						mensajes.alerta("Hubo un error al intentar enviar el comentario");
					}
				}
			});
		}
	});
});

$(document).ready(function() {
	select.addOptions('#titulacion', getTitulaciones());
	$('#titulacion').selectmenu({
		change: function() {
			var t = $('#titulacion').val();
			if (t > 0) {
				select.delOptions('#curso');
				select.addOption('#curso', 'Selecciona curso', 0);
				select.addOptions('#curso', getCursos(t));
				select.refresh('#curso', 0);
				select.refresh('#asignatura', 0);
				select.refresh('#profesor', 0);
			}
		}
	});
	$('#curso').selectmenu({
		change: function() {
			var c = $('#curso').val();
			if (c > 0) {
				select.delOptions('#asignatura');
				select.addOption('#asignatura', 'Selecciona asignatura', 0);
				select.addOptions('#asignatura', getAsignaturas(c));
				select.refresh('#asignatura', 0);
				select.refresh('#profesor', 0);
			}
		}
	});
	$('#asignatura').selectmenu({
		change: function() {
			var a = $('#asignatura').val();
			if (a > 0) {
				select.delOptions('#profesor');
				select.addOption('#profesor', 'Selecciona profesor', 0);
				select.addOptions('#profesor', getProfesores(a));
				select.refresh('#profesor', 0);
			}
		}
	});
	$('#profesor').selectmenu();
});

function getSatisfaccion(i) {
	var vector = ['Unknown', 'Muy insatisfecho', 'Insatisfecho', 'Poco satisfecho', 'Satisfecho', 'Muy satisfecho'];
	return vector[i];
}

function cargarPreguntas(data) {
	$('#def').val(data.def ? 1 : 0);
	var html = '<table>';
	var id;
	for (var i=0;i<data.preguntas.length;i++) {
		id = data.preguntas[i].id;
		html += '<tr>';
			html += '<td class="pregunta" colspan="2">'+(i+1)+' '+data.preguntas[i].pregunta+'</td>';
		html += '</tr>';
			html += '<td class="satisfaccion_td">';
				html += '<input type="hidden" data-id="'+id+'" name="respuesta_'+id+'" id="respuesta_'+id+'" />';
				html += '<span id="satisfaccion_'+id+'"></span>';
			html += '</td>';
			html += '<td class="satisfaccion_slider_td">';
				html += '<div data-id="'+id+'" class="satisfaccion_slider" style="width:200px"></div>';
			html += '</td>';
		html += '<tr>';
		html += '</tr>';
	}
	html += '</table>';
	$('#preguntas').html(html);
	
	$(".satisfaccion_slider").each(function(i, e) {
		$(e).slider({
			value:3,
			min: 1,
			max: 5,
			step: 1,
			slide: function( event, ui ) {
				$("#satisfaccion_"+$(e).attr('data-id')).html(getSatisfaccion(ui.value));
				$("#respuesta_"+$(e).attr('data-id')).val(ui.value);
			}
		});
		$("#satisfaccion_"+$(e).attr('data-id')).html(getSatisfaccion($(e).slider("value")));
		$("#respuesta_"+$(e).attr('data-id')).val($(e).slider("value"));
	});
	$('#cargando').hide();
	$('#preguntas').fadeIn();
}

//--

function getTitulaciones() {
	return data.titulaciones;
}
function getCursos(t) {
	var out = [];
	$.each(data.cursos, function(k, v) {
		if (v.titulacion == t)
			out.push(v);
	});
	return out;
}
function getAsignaturas(c) {
	var out = [];
	$.each(data.asignaturas, function(k, v) {
		if (v.curso == c)
			out.push(v);
	});
	return out;
}
function getProfesores(a) {
	var out = [];
	$.each(data.profesores_asignaturas, function(k, v) {
		if (v.asignatura == a) {
			$.each(data.profesores, function(k2, v2) {
				if (v2.id == v.profesor)
					out.push(v2);
			});
		}
	});
	return out;
}