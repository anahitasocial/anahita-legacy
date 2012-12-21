<?php defined('KOOWA') or die;?>

<div class="navbar <?= ($this->getView()->getParams()->navbarInverse) ? 'navbar-inverse' : '' ?> navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
        	<a type="button" class="btn btn-navbar" data-trigger="ShowMainmenu">
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
        	</a>
        	
            <?= @render('logo') ?>
            <div id="desktop-main-menu" class="nav-collapse collapse">
	            <?= @helper('modules.render','navigation', array('style'=>'none')) ?>
	            <?php if( $viewer_module = @helper('modules.render','viewer', array('style'=>'none'))): ?>
	            <span class="viewer"><?= $viewer_module ?></span>
	            <?php endif; ?>
            </div>
            
            <div id="mobile-main-menu" class="hidden-desktop">
            <?php if( $mobile_nav = @helper('modules.render','mobile', array('style'=>'none'))): ?>
            <?= $mobile_nav ?>
            <?php endif; ?>
            </div>
        </div>
    </div>            
</div>

<script>
var mobileMenuToggle = new Fx.Slide(document.getElement('#mobile-main-menu ul')).hide();

Delegator.register('click', {
	'ShowMainmenu' : function(event, el, api) {
		event.stop();
		mobileMenuToggle.toggle();
	},
});
</script>