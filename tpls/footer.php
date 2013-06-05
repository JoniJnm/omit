<div id="footer">
	<div style="float:left;width:350px">
		<?php
			$types = User::getUserTypes();
			$last = $types[count($types)-1];
			$userType = User::clearUserType(Request::get('userType'), true);
			$loged = false;
			foreach ($types as $type) {
				if (PHP_SCRIPT == $type || (PHP_SCRIPT == 'login' && $userType == $type))
					echo ucfirst($type);
				else
					echo '<a href="'.HTML_URL.$type.'.php">'.ucfirst($type).'</a>';
				if ($type != $last)
					echo ' | ';
				$loged = $loged || User::getInstance($type)->isLoged();
			}
			$userType = PHP_SCRIPT == 'login' ? Request::get('userType') : PHP_SCRIPT;
			if ($loged) echo ' | <a href="'.HTML_URL.'login.php?userType='.User::clearUserType($userType, true).'&salir=1">Salir</a>';
		?>
	</div>
	<div style="float:right;width:450px;text-align:right">Jónatan Núñez - Soto Montalvo &copy; OMIT (Opinion Mining In Teaching) 2012 - 2013</div>
</div>