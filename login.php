<?php

require_once(dirname(__file__).'/config.php');

if (Session::load('isAdmin')) {
	header('location: '.HTML_URL.'admin.php', true, 301);
	exit;
}

$error = '';

if (post('login')) {
	$user = post('user');
	$pass = post('pass');
	if ($user == 'admin' && $pass == 'soto') {
		Session::save('isAdmin', 1);
		header('location: '.HTML_URL.'admin.php', true, 301);
		exit;
	}
	$error = 'Usuario o contraseña incorrectos.';
}

$data = getData();

?>
<!doctype html>
<html lang="es">
<head>
	<title>Sistema de comentarios - Acceder</title>
	<script type="text/javascript">
		var data = <?php echo json_encode($data); ?>;
	</script>
	<?php require_once(PHP_TPLS.'header-common.php'); ?>
	<script type="text/javascript" src="js/login.js"></script>
	<?php if ($error) : ?>
	<script type="text/javascript">
	$(document).ready(function() {
		mensajes.add('alerta', '<?php echo $error; ?>');
	});
	</script>
	<?php endif; ?>
</head>
<body>
	<div id="main">
		<?php require_once(PHP_TPLS.'mensajes.php'); ?>
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
				<input type="hidden" name="login" value="1" />
			</form>
		</div>
		<?php require_once(PHP_TPLS.'footer-admin.php'); ?>
	</div>
</body>
</html>
