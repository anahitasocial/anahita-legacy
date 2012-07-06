<?php defined('KOOWA') or die; ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-PROFILE-INFORMATION') ?></h3>

<form data-behavior="FormValidator" action="<?=@route($entity->getURL())?>" method="post" enctype="multipart/form-data">		

	<fieldset>
		<legend><?= @text('COM-ACTORS-PROFILE-INFO-BASIC') ?></legend>
		
		<div class="control-group">
			<label class="control-label" class="control-label" for="actor-name">
				<?= @text('COM-ACTORS-NAME') ?>
			</label>
			<div class="controls">
				<input data-validators="required" class="input-input-xxlarge" size="50" maxlength="100" name="name" value="<?=$entity->name?>" type="text" />
			</div>
		</div>
		
		<?php if ( is_person($entity) ) : ?>
		<div class="control-group">
			<label class="control-label" for="actor-enabled">
				<?= @text('COM-ACTORS-PROFILE-GENDER') ?>
			</label>
			<div class="controls">
				<?= @html('select','gender', array('options'=>array('male'=>'Male','female'=>'Female','transgender'=>'Transgender','other'=>'Other'), 'selected'=>$entity->gender)) ?>
			</div>
		</div>
		<?php endif;?>
			
		<div class="control-group">
			<label class="control-label" for="actor-body">
				<?= @text('COM-ACTORS-BODY') ?>
			</label>
			<div class="controls">
				<textarea data-validators="required maxLength:1000" class="input-xxlarge" name="body" rows="5" cols="5"><?= $entity->body?></textarea>
			</div>
		</div>
		
		<?php if ( $entity->isEnableable() ) : ?>
		<div class="control-group">
			<label class="control-label" for="actor-enabled">
				<?= @text('COM-ACTORS-ENABLED') ?>
			</label>
			<div class="controls">
				<?= @html('select','enabled', array('options'=>array(@text('LIB-AN-NO'), @text('LIB-AN-YES')), 'selected'=>$entity->enabled))?>
			</div>
		</div>
		<?php endif;?>
	</fieldset>
	
	<?php foreach($profile as $header => $fields)  : ?>		
	<fieldset>
		<legend><?= @text($header) ?></legend>
		<?php foreach($fields as $label => $field) : ?>	
		<div class="control-group">
			<label><?= @text($label) ?></label>
			<div class="controls">
				<?php if (is_object($field)) : ?>
				<?php $class = ( in_array($field->name, array('textarea', 'input')) ) ? 'input-xxlarge' : '' ?>
				<?= $field->class($class)->rows(5)->cols(5) ?>
				<?php else : ?>
				<?= $field ?>
				<?php endif;?>
			</div>
		</div>
		<?php endforeach?>
	</fieldset>
	<?php endforeach;?>
	
	<div class="form-actions">			
		<input type="submit" class="btn" value="<?= @text('LIB-AN-ACTION-SAVE') ?>" />
	</div>
</form>

