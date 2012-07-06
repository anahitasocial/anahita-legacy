<?php defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="vote-count-wrapper" id="vote-count-wrapper-<?= $entity->id ?>">
<?= @helper('ui.voters', $entity); ?>
</div>

<div class="an-comments-wrapper">
<?php if (!empty($pagination)) : ?>
<?= $pagination ?>
<?php endif; ?>
<div id="an-comments-" class="an-comments an-entities">
	<?php foreach($comments as $comment) : ?>
	<?= @view('comment')->comment($comment)->strip_tags($strip_tags)->truncate_body($truncate_body)->editor($editor) ?>
	<?php endforeach; ?>
</div>

<?php if (!empty($pagination)) : ?>
<?= $pagination ?>
<?php endif; ?>
<?php if ( $can_comment ) : ?>
<?= @view('comment')->comment(null)->load('form', array('parent'=>$entity,'editor'=>$editor))?>
<?php endif;?>

<?php if ( $show_guest_prompt && !$can_comment ) : ?>
    <?php if( $viewer->guest() ) : ?>
        <?= $entity->get('type') ?>
        <?php $return = base64_encode(@route($entity->getURL())); ?>
        <?= @message(sprintf(@text('LIB-AN-MEDIUM-COMMENT-GUEST-MUST-LOGIN'), @route(array('option'=>'com_user', 'view'=>'login', 'return'=>$return))), array('type'=>'warning')) ?>
    <?php elseif ( !$entity->openToComment ) : ?>
        <?= @message('Comments are closed', array('type'=>'warning')) ?>
    <?php else : ?>
        <?= @message("Don't have permission to comment",array('type'=>'warning')) ?>
    <?php endif; ?>    
<?php endif; ?>
</div>