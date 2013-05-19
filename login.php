<?php

require_once(dirname(__file__).'/init/init.php');

$userType = Request::both('userType');
$user = User::getInstance($userType);
if (Request::get('salir')) {
	User::logout($userType);
}
$user->toHomeIfLoged();

$error = '';

if (Request::post('login')) {
	$email = Request::post('email');
	$pass = Request::post('pass');
	if ($user->login($email, $pass)) {
		$user->toHome();
	}
	$error = 'Usuario o contraseña incorrectos.';
}

?>
<!doctype html>
<html lang="es">
<head>
	<?php load('tpls.header-common'); ?>
	<title>Sistema de comentarios - Acceder</title>
	<script type="text/javascript" src="js/jquery/jquery.md5.js"></script>
	<script type="text/javascript" src="js/login.js"></script>
	<?php if ($error) : ?>
	<script type="text/javascript">
	/*<![CDATA[*/
	$(document).ready(function() {
		mensajes.alerta('<?php echo $error; ?>');
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
			<h2>Acceder como <?php echo $userType; ?></h2>
			<table>
				<tr>
					<td>Email</td>
					<td>
						<input type="text" name="email" id="email1" />
					</td>
				</tr>
				<tr>
					<td>Contraseña</td>
					<td id="td-profesor">
						<input type="password" name="pass" id="password1" />
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
			<form id="form" action="login.php?userType=<?php echo $userType; ?>" method="post" autocomplete="off" style="display:none">
				<input type="hidden" name="email" id="email2" />
				<input type="hidden" name="pass" id="password2" />
				<input type="hidden" name="login" value="1" />
			</form>
		</div>
		<?php load('tpls.footer'); ?>
	</div>
</body>
</html>
