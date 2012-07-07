<?php defined('KOOWA') or die; ?>
<?php if( $entity->voteUpCount > 0 ) : ?>
<div class="popup-header">
	<h3><?= $entity->voteUpCount == 1 ? @text('LIB-AN-VOTE-ONE-VOTED') : sprintf(@text('LIB-AN-VOTE-OTHER-VOTED'), $entity->voteUpCount)?></h3>
</div>
<div class="popup-body">
	<div class="media-grid" data-behavior="Scrollable" data-scrollable-container="!.popover-content">
		<?php foreach($entity->voteups->voter as $actor) : ?>
		<div><?= @avatar($actor) ?></div>	
		<?php endforeach; ?>
	</div>
</div>
<?php endif;?>