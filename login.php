<?php

require_once(dirname(__file__).'/config.php');

$go = Request::both('go');
if (!in_array($go, array('user', 'admin'))) $go = 'user';

if ($go == 'admin' && Session::get('isAdmin')) {
	header('location: '.HTML_URL.'admin.php', true, 301);
	exit;
}
if ($go == 'user' && Session::get('isUser')) {
	header('location: '.HTML_URL, true, 301);
	exit;
}

$error = '';

if (Request::post('login')) {
	$user = Request::post('user');
	$pass = Request::post('pass');
	if ($go == 'admin') {
		if ($user == 'admin' && $pass == 'soto') {
			Session::set('isAdmin', $user);
			header('location: '.HTML_URL.'admin.php', true, 301);
			exit;
		}
	}
	else {
		if ($user == 'user' && $pass == 'soto') {
			Session::set('isUser', $user);
			header('location: '.HTML_URL, true, 301);
			exit;
		}
	}
	$error = 'Usuario o contraseña incorrectos.';
}

?>
<!doctype html>
<html lang="es">
<head>
	<?php load('tpls.header-common'); ?>
	<title>Sistema de comentarios - Acceder</title>
	<script type="text/javascript" src="js/login.js"></script>
	<?php if ($error) : ?>
	<script type="text/javascript">
	/*<![CDATA[*/
	$(document).ready(function() {
		mensajes.add('alerta', '<?php echo $error; ?>');
	});
	/*]]>*/
	</script>
	<?php endif; ?>
</head>
<body>
	<div id="main">
		<?php load('tpls.mensajes'); ?>
		<div id="title">
			<h1>Sistema de comentarios - Acceder</h1>
		</div>
		<div id="content">
			<form action="login.php" method="post" id="form">
				<table>
					<tr>
						<td>Usuario</td>
						<td>
							<input type="text" name="user" />
						</td>
					</tr>
					<tr>
						<td>Contraseña</td>
						<td id="td-profesor">
							<input type="password" name="pass" />
						</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td><button id="acceder">Acceder</button></td>
					</tr>
				</table>
				<input type="hidden" name="go" value="<?php echo $go; ?>" />
				<input type="hidden" name="login" value="1" />
			</form>
		</div>
		<?php 
		if ($go == 'admin') load('tpls.footer-admin');
		else load('tpls.footer-front');
		?>
	</div>
</body>
</html>
