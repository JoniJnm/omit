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
	$('button').click(function() {
		$(this).removeClass('ui-state-focus');
	});
	$('.center').center();
	$('.radio').buttonset();
	$('.radio').disableSelection();
	$('input.date').attr('size', 12).attr('maxlength', 10);
	mensajes.rende();
});

function defined(v) {
	return typeof(v) != 'undefined';
}

var select = {
	addOptions: function(id, ops) {
		var html = '';
		$.each(ops, function(k, v) {
			html += '<option value="'+v.id+'">'+v.nombre+'</option>';
		});
		$(id).append(html);
	},
	addOption: function(id, name, value) {
		$(id).append('<option value="'+value+'">'+name+'</option>');
	},
	delOptions: function(id) {
		$(id).children('option').each(function(pos, obj) {
			$(obj).remove();
		});
	},
	refresh: function(id, value) {
		if (defined(value)) $(id).val(value);
		$(id).selectmenu('refresh', true);
	}
};

var mensajes = {
    alerta: function(msg) {
		$("#mensajes").html('<div class="mensaje-alerta" style="display:none">'+msg+'</div>');
		mensajes.rende();
    },
	info: function(msg) {
		$("#mensajes").html('<div class="mensaje-info" style="display:none">'+msg+'</div>');
		mensajes.rende();
    },
	borrar: function() {
		$("#mensajes").html('');
	},
	rende: function() {
		$("#mensajes").hide();
		$(".mensaje-alerta").alertStyle();
        $(".mensaje-info").infoStyle();
		$("html, body").animate({ scrollTop: 0 });
        $("#mensajes").fadeIn();
	}
};

var dialogos = {
	add: function(title, msg) {
		$('.ui-dialog').remove();
		$('#dialogos .dialogo').remove();
		var html = '<div class="dialogo" title="'+title+'"><p>'+msg+'</p></div>';
		$('#dialogos').append(html);
		$('#dialogos .dialogo').dialog();
	}
};

(function ($) {
	$.fn.alertStyle = function () {
		this.replaceWith(function (i, html) {
			var StyledError = "<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em;\">";
			StyledError += "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;margin-top:3px\">";
			StyledError += "</span><strong>Alerta: </strong>";
			StyledError += html;
			StyledError += "</p>";
			StyledError += '<img src="imagenes/cerrar.png" onclick="mensajes.borrar()">';
			StyledError += "</div>";
			return StyledError;
		});
	};
	$.fn.infoStyle = function () {
		this.replaceWith(function (i, html) {
			var StyledError = "<div class=\"ui-state-highlight ui-corner-all\" style=\"padding: 0 .7em;\">";
			StyledError += "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;margin-top:3px\">";
			StyledError += "</span><strong>Información: </strong>";
			StyledError += html;
			StyledError += "</p>";
			StyledError += '<img style="position:absolute;right:5px;top:-5px;" src="imagenes/cerrar.png" onclick="mensajes.borrar()">';
			StyledError += "</div>";
			return StyledError;
		});
	};
})(jQuery);

(function ($) {
	$.fn.extend({
		center: function () {
			return this.each(function () {
				var top = ($(window).height() - $(this).outerHeight()) / 2;
				var left = ($(window).width() - $(this).outerWidth()) / 2;
				$(this).css({
					position: 'absolute',
					margin: 0,
					top: (top > 0 ? top : 0) + 'px',
					left: (left > 0 ? left : 0) + 'px'
				});
			});
		}
	});
})(jQuery);

(function($){
    $.fn.disableSelection = function() {
        return this
                 .attr('unselectable', 'on')
                 .css('user-select', 'none')
                 .on('selectstart', false);
    };
})(jQuery);