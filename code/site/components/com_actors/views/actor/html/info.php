<div id="actor-extended-info">
<?php foreach($profile as $header => $values)  : ?>
	<div class="info-container">		
		<h4><?= @text($header) ?></h4>
		<?php foreach($values as $label => $value) : ?>
		<dl>
			<dt><?= @text($label) ?></dt>
			<dd><?= @escape(@text($value)) ?></dd>
		</dl>
		<?php endforeach?>
	</div>
<?php endforeach;?>
</div>