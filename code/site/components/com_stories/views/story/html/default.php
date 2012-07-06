<?php defined('KOOWA') or die ?>

<module position="sidebar-b" style="basic"></module>

<div class="an-story an-entity an-record an-removable">
    <div class="story-avatar">
        <?= @avatar($subject) ?>
    </div>      
    <div class="story-container"> 
        <h4 class="story-title"><?= $title ?></h4>
        <?php if ( !empty($body) ) : ?>
        <div class="story-body">
            <?= $body ?>
        </div>
        <?php endif; ?>
        
        <div class="story-timestamp">
            <?php if ( !empty($icon) ) : ?>
                <img width=10 class="an-story-icon" src="base://<?=$icon?>" />                  
            <?php endif; ?>
            <?= @date($timestamp) ?>
        </div>        
        
        <div class="story-comments">
        	<?= @helper('ui.comments', $story) ?>  
        </div>   
    </div>
</div>