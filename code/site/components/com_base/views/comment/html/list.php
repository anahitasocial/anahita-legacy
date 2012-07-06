<?php defined('KOOWA') or die('Restricted access') ?>
<div scroll-handle="<?=$comment->id?>" id="an-comment-<?= $comment->id ?>" class="an-comment an-record an-removable">
	<div class="comment-author-avatar">
		<?= @avatar($comment->author)  ?>
	</div>
	
    <?php $body = $comment->body; ?>  
	<?php $body = @content($body) ?>
	<?php if (empty($truncate_body) ) : ?>
	<?php $body =  stripslashes( $body ) ?>
	<?php else : ?>
	<?php $body = @helper('text.truncate', stripslashes( $body ), is_bool($truncate_body) ? array() : $truncate_body) ?>	
	<?php endif;?>
	
	<div class="comment-box">
		<div class="comment-body">
			<span class="comment-author"><?= @name($comment->author) ?></span>  
			<span><?= $body ?></span>
		</div>	
		
		<div class="an-meta">
			<?=@date($comment->creationTime) ?> 
			<a href="<?= @route($comment->parent->getURL().'#permalink='.$comment->id) ?>">#</a>
		</div>
		
		<?= @helper('ui.commands', @commands('list', array('entity'=>$comment))) ?>
		
		<div id="vote-count-wrapper-<?= $comment->id ?>">
			<?= @helper('ui.voters', $comment); ?>
		</div>
	</div>
</div>
