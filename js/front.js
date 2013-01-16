$(document).ready(function() {
	$('#enviar').click(function() {
		var asignatura = $('#asignatura').val();
		var profesor = $('#profesor').val();
		var comentario = $('#comentario').val();
		if (asignatura <= 0)
			mensajes.add('alerta', "Seleccione una asignatura");
		else if (profesor <= 0)
			mensajes.add('alerta', "Seleccione un profesor");
		else if (comentario.length == 0)
			mensajes.add('alerta', "Introduzca un comentario");
		else
			$.ajax({
				url: USER_CONTROLLER,
				type: 'POST',
				data: "task=insertarComentario&asignatura="+asignatura+"&profesor="+profesor+"&comentario="+encodeURIComponent(comentario)
			});
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
	var vector = ['Muy insatisfecho', 'Insatisfecho', 'Poco satisfecho', 'Satisfecho', 'Muy satisfecho'];
	return vector[i];
}

$(document).ready(function() {
	$(".satisfaccion_slider").each(function(i, e) {
		$(e).slider({
			value:2,
			min: 0,
			max: 4,
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