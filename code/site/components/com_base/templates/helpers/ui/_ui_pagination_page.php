<?php defined('KOOWA') or die('Restricted access') ?>

<?php if ( count($pages) > 1 ) : ?>
<div class="pagination" data-behavior="Pagination">
	<ul>	    	
		<li class="prev <?= $prev_page ? '' : 'disabled'?>">
			<a href="<?= $prev_page ?>">
				<?= @text('PREV') ?>
			</a>
		</li>			
		<?php foreach($pages as $page) : ?>
			<li class="<?= $page['current'] ? 'active' : ''?>">			
				<a href="<?=$page['url']?>">
					<?= $page['number'] ?>
				</a>			
			</li>				
		<?php endforeach; ?>
		<li class="next <?= $next_page ? '' : 'disabled'?>">
			<a href="<?= $next_page ?>">
				<?= @text('NEXT') ?>
			</a>
		</li>						
	</ul>
</div>

<p><?= sprintf(@text('LIB-AN-RECORDS-AVAILABLE'), number_format($total)) ?></p>
<?php endif; ?>