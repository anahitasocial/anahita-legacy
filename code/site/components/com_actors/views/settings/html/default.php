<?php defined('KOOWA') or die('Restricted access');?>


<module position="sidebar-a" title="<?= @text('COM-ACTORS-PROFILE-EDIT') ?>">
<ul id="setting-tabs" class="nav nav-pills nav-stacked" >
<?php foreach($tabs as $tab) : ?>
	<li class="<?= $tab->active ? 'active' : ''?>">        
		<a href="<?=@route($tab->url)?>">
            <?= @text($tab->label)?>
        </a>
	</li>
<?php endforeach;?>
</ul>
</module>

<div class="actor-settings">
<?= $content ?>
</div>