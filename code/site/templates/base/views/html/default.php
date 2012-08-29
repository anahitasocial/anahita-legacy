<?php defined('KOOWA') or die;?>
<!DOCTYPE html>
<html>
    <head>
        <?= @render('style') ?>        
    </head>
    <body>
        <script src="media://lib_anahita/js/min/site.js"></script>
        <?= @template('tmpl/navbar') ?>
            
        <div class="container" id="container-system-message">
            <?= @render('messages') ?>
        </div>
        
        <?= @render('modules', 'header') ?>
        <?= @render('modules', 'showcase', array('style'=>'none')) ?>
        <?= @render('modules', 'feature', array('style'=>'simple')) ?>
        <?= @render('modules', 'utility', array('style'=>'none')) ?>
        <?= @render('modules', 'maintop', array('style'=>'simple')) ?>
        
        <?= @render('component') ?>
        
        <?= @render('modules', 'mainbottom', array('style'=>'simple')) ?>
        
        <?php if ( $bottom = @render('modules', 'bottom', array('style'=>'simple')) ) : ?>
        <div id="bottom-wrapper">
            <?= $bottom ?>
        </div>
        <?php endif; ?> 
        
        <?php if ( $footer = @render('modules', 'footer', array('style'=>'simple')) ) : ?>
        <div id="footer-wrapper">
            <?= $footer ?>
        </div>
        <?php endif; ?>
        
        <div class="container">
            <?= @render('copyright') ?>
        </div>
        
        <?= @render('analytics') ?>
    </body>
</html>