<?php defined('KOOWA') or die ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-DELETE') ?></h3>

<form action="<?=@route($entity->getURL())?>" method="post">
	<input type="hidden" name="action" value="delete" />	
	
	<div class="alert-message block-message error">
  		<p><?= $msg = sprintf(translate(array($entity->component.'-DELETE-PROMPT','COM-ACTORS-DELETE-PROMPT'))) ?></p>
  		
  		<div class="alert-actions">
  			<button data-trigger="Remove" data-remove-form="!form" data-remove-confirm-message="<?= $msg?>"  class="btn danger"><?= @text('LIB-AN-ACTION-DELETE') ?></button>
  		</div>
	</div>
</form>