<?php defined('KOOWA') or die;?>
<!DOCTYPE html>
<html>
    <head>
        <?= @render('style') ?>                
    </head>
    <body>
        <script src="media://lib_anahita/js/min/site.js"></script>
        <div class="navbar <?= ($this->getView()->getParams()->navbarInverse) ? 'navbar-inverse' : '' ?> navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <?= @render('logo') ?>                    
                </div>
            </div>            
        </div>        
            <div class="container">
                <div class="row"> 
                    <div class="span8 offset2">               
                        <div class="hero-unit">
                            <p><?= JFactory::getApplication()->getCfg('offline_message'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="row" id="row-main">
                    <div class="span4 offset4">
                    <form data-behavior="FormValidator" class="well form-inline" action="<?=@route()?>" method="post">
                        <fieldset>
                            <legend><?= @text('LOGIN') ?></legend>
                                        
                                <input type="hidden" name="remember" value="yes">
                                <input type="hidden" name="option" value="com_user">
                                <input type="hidden" name="task" value="login">
                                <input type="hidden" name="return" value="<?= $return_url ?>" />
                                <?php echo JHTML::_( 'form.token' ); ?>     
                                <?= @helper('ui.form', array(
                                    'USERNAME'          => @html('textfield',     'username', '')->dataValidators('required'),
                                    'PASSWORD'          => @html('passwordfield', 'passwd',   '')->dataValidators('required')
                                ))?>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary"><?=@text('LOGIN')?></button>
                                </div>          
                        </fieldset>
                    </form>
                    </div>
                </div>
            </div>       
        
        <?= @render('analytics') ?>
    </body>
</html>