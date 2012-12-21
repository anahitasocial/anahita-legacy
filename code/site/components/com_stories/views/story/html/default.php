<?php defined('KOOWA') or die ?>

<module position="sidebar-b" style="simple"></module>

<div class="an-entity">
    <div class="entity-portrait-square">
        <?= @avatar($subject) ?>
    </div>
          
    <div class="entity-container"> 
        <h3 class="entity-title"><?= $title ?></h4>
        <?php if ( !empty($body) ) : ?>
        <div class="entity-body">
            <?= $body ?>
        </div>
        <?php endif; ?>
        
        <div class="entity-meta">
            <?= @date($timestamp) ?>
        </div>
    </div>
</div>

<?= @helper('ui.comments', $story) ?>  