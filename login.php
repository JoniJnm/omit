<?php

/**
 * Página de login
 */

require_once(dirname(__file__).'/init/init.php');

$userType = Request::both('userType');
$user = User::getInstance($userType);
$user->toHomeIfLoged();

?>
<!doctype html>
<html lang="es">
<head>
	<?php load('tpls.header-common'); ?>
	<title>OMIT (Opinion Mining In Teaching) - Acceder</title>
	<script type="text/javascript" src="js/jquery/jquery.md5.js"></script>
	<script type="text/javascript" src="js/login.js"></script>
</head>
<body>
	<div id="main">
		<?php load('tpls.mensajes'); ?>
		<div id="title">
			<a href=""><h1>OMIT (Opinion Mining In Teaching) - Acceder</h1></a>
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
			<form id="form" action="<?php echo LOGIN_CONTROLLER; ?>" method="post" autocomplete="off" style="display:none">
				<input type="hidden" name="email" id="email2" />
				<input type="hidden" name="pass" id="password2" />
				<input type="hidden" name="userType" value="<?php echo $userType; ?>" />
				<input type="hidden" name="task" value="login" />
			</form>
		</div>
		<?php load('tpls.footer'); ?>
	</div>
</body>
</html>
