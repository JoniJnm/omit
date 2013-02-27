var parte = 0;

function irParte(from, to) {
	if ($('#parte'+to).length == 0) return;
	
	if (from == 1) {
		var asignatura = $('#asignatura').val();
		var profesor = $('#profesor').val();
		if (asignatura <= 0) {
			mensajes.add('alerta', "Seleccione una asignatura");
			return;
		}
		else if (profesor <= 0) {
			mensajes.add('alerta', "Seleccione un profesor");
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
}

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
		if (asignatura <= 0) {
			mensajes.add('alerta', "Seleccione una asignatura");
		}
		else if (profesor <= 0) {
			mensajes.add('alerta', "Seleccione un profesor");
		}
		else if (comentario.length == 0) {
			mensajes.add('alerta', "Introduzca un comentario");
		}
		else {
			mensajes.borrar();
			var respuestas = [];
			$('.satisfaccion_td input').each(function(i, e) {
				var id = $(e).attr('data-id');
				var v = $(e).val();
				respuestas.push(id+":"+v);
			});
			respuestas = respuestas.join(';');
			$.ajax({
				url: ALUMNO_CONTROLLER,
				type: 'POST',
				dataType: 'test',
				data: "task=insertarComentario&asignatura="+asignatura+"&profesor="+profesor+"&comentario="+encodeURIComponent(comentario)+"&respuestas="+respuestas,
				complete: function(data, textStatus, jqXHR ) {
					if (data.responseText == "OK") {
						mensajes.add('info', "Comentario añadido");
						select.refresh('#profesor', 0);
						irParte(parte, 1);
						$('#comentario').val('');
						$(".satisfaccion_slider").each(function(i, e) {
							$(e).slider('value', 3);
						});
					}
					else {
						mensajes.add('alerta', "Hubo un error al intentar enviar el comentario");
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

$(document).ready(function() {
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
});