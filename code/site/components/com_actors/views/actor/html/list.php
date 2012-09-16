<?php defined('KOOWA') or die ?>

<?php $commands = @commands('list') ?>

<?php $highlight = ($item->enabled) ? '' : 'an-highlight' ?>
<div class="an-entity an-record dropdown-actions <?= $highlight ?>" data-behavior="BS.Dropdown">
	<div class="actor-portrait">
		<?= @avatar($item) ?>
	</div>
	
	<div class="actor-container">
		<h3 class="actor-name"><?= @name($item) ?></h3>
		
		<div class="an-meta">
			<?= $item->followerCount ?>
			<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span> 
			
			<?php if($item->isLeadable()): ?>
			/ <?= $item->leaderCount ?>
			<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
			<?php endif; ?>
		</div>
		
		<div class="actor-description">
			<?= @helper('text.truncate',strip_tags($item->description), array('length'=>200)); ?>
		</div>
				
		<?php if ( count($commands) ) : ?>
		<ul class="an-actions">
			<?php if ( $action = $commands->extract('follow') ) : ?>
				<li><?= @helper('ui.command', $action->class('btn btn-primary btn-small'))?></li>
			<?php elseif ( $action = $commands->extract('unfollow') ) : ?>
				<li><?= @helper('ui.command', $action->class('btn btn-small'))?></li>
			<?php endif;?>
			
			<?php foreach($commands as $action) : ?>
				<li><?= @helper('ui.command', $action) ?></li>
			<?php endforeach;?>
		</ul>
		<?php endif; ?>
	</div>
</div>