<?php defined('KOOWA') or die;?>
<!DOCTYPE html>
<html>
    <head>
        <?= @render('style') ?>                
    </head>
    <body>
        <script src="media://lib_anahita/js/min/site.js"></script>
        <?= @template('tmpl/navbar') ?>
      
        <?= @render('component') ?>
        
        <div class="container">
            <?= @render('copyright') ?>
        </div>
        
        <?= @render('analytics') ?>
    </body>
</html>
