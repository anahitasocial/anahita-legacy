<?php defined('KOOWA') or die('Restricted access');?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-REQUESTS') ?></h3>

<div id="an-actors" class="an-entities an-actors">
    <?php foreach($entity->requesters as $actor ) : ?>
    <div class="an-entity an-record">
        <div class="actor-portrait">
            <?= @avatar($actor) ?>
        </div>
        
        <div class="actor-container">
            <h3 class="actor-name"><?= @name($actor) ?></h3>
            
            <div class="an-meta">
                <?= $actor->followerCount ?>
                <span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span> 
                / <?= $entity->leaderCount ?>
                <span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
            </div>
            
            <div class="actor-description">
            <?= @helper('text.truncate',strip_tags($actor->description), array('length'=>200)); ?>
            </div>
                
            <div class="an-actions">
                <button data-trigger="Submit" href="<?= @route($entity->getURL().'&action=addblocked&actor='.$actor->id) ?>" class="btn">
                    <i class="icon-ban-circle"></i>&nbsp;<?= @text('COM-ACTORS-SOCIALGRAPH-BLOCK') ?>
                </button>
                <button data-trigger="Submit" href="<?= @route($entity->getURL().'&action=ignorerequester&requester='.$actor->id) ?>" class="btn">
                    <i class="icon-remove"></i>&nbsp;<?= @text('LIB-AN-ACTION-REMOVE') ?>
                </button>            
                <button data-trigger="Submit" href="<?= @route($entity->getURL().'&action=confirmrequester&requester='.$actor->id) ?>" class="btn">
                    <i class="icon-ok"></i>&nbsp;<?= @text('LIB-AN-ACTION-CONFIRM') ?>
                </button>                            
            </div>
        </div>
    </div>      
    <?php endforeach; ?>
</div>