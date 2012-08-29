<?php defined('KOOWA') or die ?>
<?php $options = empty($options) ? array() : $options ?>

<?php @listItemView()->layout('list')->set($options) ?>

<?php if(count($items)) :?>
<div id="an-actors" class="an-entities an-actors">
	<?php foreach($items as $item ) : ?>
		<?= @listItemView()->item($item)?>
	<?php endforeach; ?>
</div>

<?php if ( !isset($pagination) || $pagination !== false ) : ?>
<div id="an-more-records" class="an-more-records">
<?= empty($pagination) ? @pagination($items, array('url'=>@route('layout=list'))) : $pagination?>
</div>
<?php endif;?>

<?php else : ?>
<?= @message(@text('LIB-AN-PROMPT-NO-MORE-RECORDS-AVAILABLE')) ?>
<?php endif; ?>