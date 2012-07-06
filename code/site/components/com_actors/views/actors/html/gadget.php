<?php defined('KOOWA') or die; ?>

<?php $options = empty($options) ? array() : $options ?>

<?php @listItemView()->layout('gadget')->set($options) ?>

<?php if(count($entities)) :?>
<div class="gadget-entities an-entities">
	<?php foreach($entities as $entity ) : ?>
	<?= @listItemView()->entity($entity) ?>
	<?php endforeach; ?>
</div>

<div id="an-more-records" class="an-more-records">
	<?= @pagination($entities, @route(array('layout'=>'gadget'))) ?>
</div>

<?php else : ?>
<?= @message(@text('LIB-AN-PROMPT-NO-MORE-RECORDS-AVAILABLE')) ?>
<?php endif; ?>