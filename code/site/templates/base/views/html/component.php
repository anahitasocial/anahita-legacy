<?php defined('KOOWA') or die;?>
<!DOCTYPE html>
<html>
    <head>
        <?= @render('style') ?>        
    </head>
    <body id="tmpl-component">        
        <script src="media://lib_anahita/js/min/site.js"></script>
        
        <div class="container" id="container-system-message">
            <?= @render('messages') ?>
        </div>
        
        <?= @render('component') ?>
        <?= @render('analytics') ?>
    </body>
</html>