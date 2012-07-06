<?php defined('KOOWA') or die ?>
<?php
$dates = @helper('notifications.group', $notifications);
?>
<?php foreach($dates as $date => $notifications) : ?>
<h4><?=$date?></h4>
<div id="com-notifications-list" class="an-entities an-actors">
    <?php foreach($notifications as $notification) : ?>
    <div class="an-entity an-record an-removable">
    	<div class="actor-portrait">
    		<?= @avatar($notification->subject) ?>
    	</div>
    	<div class="actor-container">	        
	        <div class="actor-description">
	        <?php $data = @helper('parser.parse', $notification, $actor)?>
	        <?= $data['title']?>
        	</div>
        	<div class="entity-meta">
                <?= $notification->createdOn->format('%l:%M %p')?>
        	</div>
        </div>
    </div>
    <?php endforeach;?>
</div>

<?php endforeach; ?>

<?php if (count($dates) == 0) : ?>
<?= @message(@text('COM-NOTIFICATIONS-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>