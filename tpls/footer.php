<div id="footer">
	<div style="float:left;width:500px">
		<?php
			$tmp = explode('/', $_SERVER['SCRIPT_NAME']);
			$tmp = $tmp[count($tmp)-1];
			if (strlen($tmp) > 4) $tmp = substr($tmp, 0, -4);
			$types = User::getUserTypes();
			foreach ($types as $type) {
				if ($type != $types[0]) echo ' | ';
				if ($tmp == $type)
					echo $type;
				else
					echo '<a href="'.HTML_URL.$type.'.php">'.$type.'</a>';
			}
		?>
	</div>
	<div style="float:right;width:300px;text-align:right">Jónatan Núñez - Soto Montalvo &copy; 2012 - 2013</div>
</div>