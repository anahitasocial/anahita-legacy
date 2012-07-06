<?php defined('KOOWA') or die; ?>

<div class="gadget-entity an-entity">
	<div class="entity-thumbnail">
		<?= @avatar($entity) ?>
	</div>
	
	<div class="entity-container">
		<h4 class="entity-title"><?= @name($entity) ?></h4>
		
		<div class="an-meta">
			<?= $entity->followerCount ?> 
			<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span> 
		</div>
		
		<div class="entity-description">
			<?= @helper('text.truncate', strip_tags($entity->description), array('length'=>200)); ?>
		</div>
	</div>
</div>
