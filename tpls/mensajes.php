<div id="mensajes">
	<?php $mensajes = Mensajes::getMensajes(); ?>
	<?php if ($mensajes) :?>
		<?php foreach ($mensajes as $m): //tipo=alerta|info ?>
		<div class="<?php echo 'mensaje-'.$m->tipo; ?>" style="display:none"><?php echo $m->msg; ?></div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
<div id="dialogos"></div>