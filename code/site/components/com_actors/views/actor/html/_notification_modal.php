<?php defined('KOOWA') or die('Restricted access') ?>
<?php if ( !$viewer->eql($item) ) : ?>
<div data-behavior="BS.Popup" class="modal hide" id="notification-modal">
  
  <div class="modal-header">
    <a href="#" class="close">x</a>
    <h3><?= @text('COM-NOTIFICATIONS-EDIT-NOTIFICATION-SETTINGS') ?></h3>
  </div>
  
  <div class="modal-body">
        <?php if ( $item->authorize('subscribe') ) : ?>
		<form action="<?=@route($item->getURL())?>" method="post" class="form-horizontal">
            <div class="control-group">
                <label class="control-label"><?= @text('COM-NOTIFICATIONS-RECIEVE-NOTIFICATIONS')?></label>            
                <div class="controls">                    
                    <input type="hidden" name="action" value="togglesubscription" />
                    <select onchange="this.form.ajaxRequest().post()" >
                        <?= @html('options', array(@text('COM-NOTIFICATIONS-RECIEVE-NOTIFICATIONS-NEW-SB'),@text('COM-NOTIFICATIONS-RECIEVE-NOTIFICATIONS-ONLY-SB')),$item->subscribed($viewer) ? 0 : 1) ?>
                    </select>
                </div>
            </div>
        </form>  
        <?php endif; ?>
        <?php 
            $setting = @service('repos:notifications.setting')->findOrCreate(array(
                'person' => $viewer,
                'actor'  => $item
            ))->reset();      
        ?>
        <form action="<?= @route('option=com_notifications&view=setting&oid='.$item->id)?>" method="post" class="form-horizontal">                      
			<div class="control-group">
				<label class="control-label"><?= @text('COM-NOTIFICATIONS-SEND-EMAIL')?></label>
				<div class="controls">
					<label class="checkbox">
						<input onclick="this.form.ajaxRequest().post()" id="notification-email" name="email" <?= $setting->getValue('posts', 1) == 2 ? 'disabled="true"' : ''?> <?= $setting->sendEmail('posts', 1) && $setting->getValue('posts', 1) < 2 ? 'checked' : ''?> value="1" type="checkbox" />
						<?= $viewer->email?><a href="<?=@route($viewer->getURL().'&get=settings&edit=account')?>" class="btn-mini"><?= @text('COM-ACTORS-NOTIFICATION-CHANGE-EMAIL') ?></a>
					</label>
				</div>
			</div>
		</form>
  </div>
  <div class="modal-footer">
	<a href="#" class="btn dismiss"><?= @text('LIB-AN-ACTION-DONE') ?></a>
  </div>
</div>
<?php endif; ?>
