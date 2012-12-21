<?php defined('KOOWA') or die; ?>

<module position="sidebar-b" style="simple"></module>

<?= @helper('ui.searchbox', @route('layout=list'))?>

<div data-behavior="InfinitScroll" data-infinitscroll-options="{'url':'<?= @route('layout=list') ?>'}" class="an-entities" id="an-entities-main">
<?= @template('list') ?>
</div>

<div class="an-loading-prompt hide">
	<?= @message(@text('LIB-AN-LOADING-PROMPT')) ?>
</div>