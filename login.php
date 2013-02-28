<?php

require_once(dirname(__file__).'/config.php');

if (Request::get('salir')) User::salir();

$userType = User::clearUserType(Request::both('userType'));
!User::isLoged($userType) or User::toHome($userType);

$error = '';

if (Request::post('login')) {
	$user = Request::post('user');
	$pass = Request::post('pass');
	if ($userType == 'admin') {
		if ($user == 'admin' && $pass == 'soto') {
			Session::set('isAdmin', $user);
			User::toHome('admin');
		}
	}
	elseif ($userType == 'profesor') {
		if ($user == 'profesor' && $pass == 'soto') {
			Session::set('isProfesor', $user);
			User::toHome('profesor');
		}
	}
	elseif ($userType == 'alumno') {
		if ($user == 'alumno' && $pass == 'soto') {
			Session::set('isAlumno', $user);
			User::toHome('alumno');
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
			<h2>Acceder como <?php echo $userType; ?></h2>
			<form action="login.php?userType=<?php echo $userType; ?>" method="post" id="form">
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
		<?php load('tpls.footer'); ?>
	</div>
</body>
</html>
