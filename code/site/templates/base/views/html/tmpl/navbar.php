<div class="navbar <?= ($this->getView()->getParams()->navbarInverse) ? 'navbar-inverse' : '' ?> navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <?= @render('logo') ?>
            <?= @helper('modules.render','navigation', array('style'=>'none')) ?>
            <?php if( $viewer_module = @helper('modules.render','viewer', array('style'=>'none'))): ?>
            <span class="viewer"><?= $viewer_module ?></span>
            <?php endif; ?>
        </div>
    </div>            
</div>