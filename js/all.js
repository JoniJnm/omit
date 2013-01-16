$(document).ready(function() {
	$('.tabla_horizontal tr').each(function() {
		$(this).children('td').eq(0).addClass('td-left');
		$(this).children('td').eq(1).addClass('td-right');
		$(this).children('td').eq(2).addClass('td-left td-right');
		$(this).children('td').eq(3).addClass('td-right');
	});
	$('input[placeholder], textarea[placeholder]').placeholder();
	$('textarea, input').addClass('ui-widget ui-state-default ui-corner-all');
	$('button').button();
	$('.center').center();
	mensajes.rende();
});

function defined(v) {
	return typeof(v) != 'undefined';
}

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

var select = {
	addOptions: function(p, ops) {
		var html = '';
		$.each(ops, function(k, v) {
			html += '<option value="'+v.id+'">'+v.nombre+'</option>';
		});
		$(p).append(html);
	},
	addOption: function(p, name, value) {
		$(p).append('<option value="'+value+'">'+name+'</option>');
	},
	delOptions: function(p) {
		$(p).children('option').each(function(pos, obj) {
			$(obj).remove();
		});
	},
	refresh: function(p, s) {
		if (defined(s)) $(p).val(s);
		$(p).selectmenu('refresh', true);
	}
};

var mensajes = {
    add: function(tipo, msg) {
        $("#mensajes").append('<div class="mensaje-'+tipo+'" style="display:none">'+msg+'</div>');
		mensajes.rende();
    },
	rende: function() {
		$(".mensaje-alerta").alertStyle();
        $(".mensaje-info").infoStyle();
        $(".ui-state-error, .ui-state-highlight").fadeIn('slow', function() {
            var elm = this;
            setTimeout(function() {
                $(elm).fadeOut('slow', function() {
                    $(elm).remove();
                });
            }, 3000);
        });
	}
};
(function($) {
    $.fn.alertStyle = function() {
        this.replaceWith(function(i,html){
            var StyledError = "<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em;\">";
            StyledError += "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;margin-top:3px\">";
            StyledError += "</span><strong>Alerta: </strong>";
            StyledError += html;
            StyledError += "</p></div>";
            return StyledError;
        });
    };
    $.fn.infoStyle = function() {
        this.replaceWith(function(i,html){
            var StyledError = "<div class=\"ui-state-highlight ui-corner-all\" style=\"padding: 0 .7em;\">";
            StyledError += "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;margin-top:3px\">";
            StyledError += "</span><strong>Información: </strong>";
            StyledError += html;
            StyledError += "</p></div>";
            return StyledError;
        });
    }
})(jQuery);

(function($){
    $.fn.extend({
        center: function () {
            return this.each(function() {
                var top = ($(window).height() - $(this).outerHeight()) / 2;
                var left = ($(window).width() - $(this).outerWidth()) / 2;
                $(this).css({position:'absolute', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
            });
        }
    }); 
})(jQuery);
