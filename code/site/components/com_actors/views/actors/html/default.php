<?php defined('KOOWA') or die; ?>

<module position="sidebar-b" style="simple"></module>

<?= @helper('ui.searchbox', @route('layout=list'))?>

<div class="an-entities-wrapper">
	<div data-behavior="InfinitScroll" data-infinitscroll-options="{'url':'<?= @route('layout=list') ?>'}" class="an-entities" id="an-entities-main">
	<?= @template('list') ?>
	</div>
</div>