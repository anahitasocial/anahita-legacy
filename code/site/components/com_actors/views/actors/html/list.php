<?php defined('KOOWA') or die ?>
<?php $options = empty($options) ? array() : $options ?>

<?php @listItemView()->layout('list')->set($options) ?>

<?php if(count($entities)) :?>
<div id="an-actors" class="an-entities an-actors">
	<?php foreach($entities as $entity ) : ?>
		<?= @listItemView()->entity($entity)?>
	<?php endforeach; ?>
</div>

<?php if ( !isset($pagination) || $pagination !== false ) : ?>
<div id="an-more-records" class="an-more-records">
<?= empty($pagination) ? @pagination($entities, array('url'=>@route('layout=list'))) : $pagination?>
</div>
<?php endif;?>

<?php else : ?>
<?= @message(@text('LIB-AN-PROMPT-NO-MORE-RECORDS-AVAILABLE')) ?>
<?php endif; ?>