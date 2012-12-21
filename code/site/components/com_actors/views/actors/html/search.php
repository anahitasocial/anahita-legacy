<?php defined('KOOWA') or die; ?>

<module position="sidebar-b" style="none"></module>

<?= @helper('ui.searchbox', @route('layout=list'))?>

<div data-behavior="InfinitScroll" data-infinitscroll-options="{'url':'<?= @route('layout=list') ?>'}" class="an-entities" id="an-entities-main"></div>
