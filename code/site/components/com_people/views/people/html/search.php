<?php defined('KOOWA') or die; ?>

<module position="sidebar-b"></module>

<?= @helper('ui.searchbox', @route('view=people&layout=list'))?>

<div id="an-entities-wrapper" class="an-entities-wrapper"></div>