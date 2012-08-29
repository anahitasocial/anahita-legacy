<?php defined('KOOWA') or die('Restricted access');?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-APPS') ?></h3>

<div class="an-entities an-apps">
	<?php foreach($enablable_apps as $app ) : ?>
	<div class="an-entity an-app" scroll-handle="<?=$app->id?>">
		<div class="app-actions">
			<?php if ( $app->authorize('install', array('actor'=>$item)) ) : ?>
			<a class="btn btn-primary" data-trigger="Submit" href="<?=@route($item->getURL().'&action=addapp&app='.$app->component)?>">
				<?= @text('COM-ACTORS-APP-ACTION-INSTALL') ?>
			</a>						
			<?php else : ?>
			<a class="btn" data-trigger="Submit" href="<?=@route($item->getURL().'&action=removeapp&app='.$app->component)?>">
				<?= @text('COM-ACTORS-APP-ACTION-UNINSTALL') ?>
			</a>
			<?php endif;?>	
		</div>	
		
		<div class="app-container">
			<h4 class="app-title"><?= $app->getName() ?></h4>
			<div class="app-description"><?= $app->getDescription() ?></div>
			<?php if ( count($app->getFeatures()) ) : ?>
			<div class="app-features">
			    <form>	
				    <div class="control-label">
				    	<label class="control-label" for="<?= $app->getName() ?>"><?= @text('COM-ACTORS-APP-PROFILE-FEATURES') ?></label>
				    	<div class="controls">		    			        
	    			    <?php foreach($app->getFeatures() as $feature) : ?>
	    			        <label class="checkbox">
	    			        <?php $enabled = !$app->authorize('install', array('actor'=>$item)) ?>
	    			            <input type="checkbox" disabled <?= $enabled ? 'checked' : ''?>/>
	    			            <?= translate(array(strtoupper('COM-'.$app->getName().'-FEATURE-'.$feature),strtoupper('COM-ACTORS-APP-PROFILE-FEATURE-'.$feature))) ?>
	    			        </label>
	    			    <?php endforeach; ?>		
	    			    </div>	
	    			</div>    
			    </form>
			</div>
			<?php endif;?>
		</div>
	</div>
	<?php endforeach;?>
</div>
