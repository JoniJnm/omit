function acceder() {
	$('#email2').val($('#email1').val());
	$('#password2').val($.md5($('#password1').val()));
	document.getElementById('form').submit();
}

$(document).ready(function() {
	$('#acceder').click(function() {
		acceder();
	});
});