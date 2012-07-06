<?php defined('KOOWA') or die; ?>

<div id="actor-profile">

<module position="sidebar-b">
	<?= @helper('ui.gadget', $gadgets->extract('socialgraph')) ?>	
</module>

<module position="sidebar-a">
<?= @avatar($entity, 'medium', false) ?>
</module>

<?php if ( count($gadgets) > 1 ) : ?>
<module position="sidebar-a">	
	<ul class="nav nav-pills nav-stacked sidelinks" data-behavior="BS.Tabs" data-bs-tabs-options="{'smooth':true,'tabs-selector':'.profile-tab-selector a','sections-selector':'! * .profile-tab-content'}">
		<?php foreach($gadgets as $index=>$gadget) : ?>
			<li class="profile-tab-selector <?= ($index == 'stories') ? 'active' : ''; ?>">
				<a href="#"><?= $gadget->title ?></a>
			</li>
		<?php endforeach;?>
	</ul>
</module>
<?php endif; ?>

<h2 id="actor-name"><?= @name($entity, false) ?></h2>

<?php if(!empty($entity->body)): ?>
<div id="actor-description">
	<?= @helper('text.truncate', @escape($entity->body), array('length'=>250, 'read_more'=>true)); ?>
</div>
<?php endif; ?>
<?= @helper('com://site/composer.template.helper.ui.composers', $composers) ?>
<?php foreach($gadgets as $gadget) : ?>
<div class="profile-tab-content">		
	<?= @helper('ui.gadget', $gadget) ?>
</div>		
<?php endforeach;?>

</div>