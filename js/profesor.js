$(document).ready(function() {
	$('#asignatura').selectmenu({
		change: function() {
			var id = $('#asignatura').val();
			if (id > 0) {
				$('#preguntas').html('');
				$.ajax({
					url: PROFESOR_CONTROLLER,
					data: 'task=getAsignaturas&asignatura='+id,
					type: 'post',
					dataType: 'json',
					success: onLoadAsignaturas
				});
				
			}
			else {
				$('#preguntas_div').hide();
			}
		}
	});
	$('#guardar').click(function() {
		document.getElementById('profesorForm').submit();
	});
});

function onLoadAsignaturas(data) {
	$('#preguntas_div').show();
	var def = !(data.length>0) || !data[0].id;
	var html = '';
	for (var i=0; i<data.length; i++) {
		html += '<div>Pregunta '+(i+1)+' <input style="width:650px" type="text" value="'+data[i].pregunta+'" name="pregunta_'+(data[i].id?data[i].id:(i+1))+'" /></div>';
	}
	$('#def').val(def ? 1 : 0);
	$('#preguntas').html(html);
	$('#preguntas input').addClass('ui-widget ui-state-default ui-corner-all');
}