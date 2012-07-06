<?php defined('KOOWA') or die; ?>

<module position="sidebar-b"></module>

<?= @helper('ui.searchbox', @route('layout=list'))?>

<div id="an-entities-wrapper">
<?= @template('list') ?>
</div>