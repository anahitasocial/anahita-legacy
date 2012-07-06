<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// load and inititialize gantry class
require_once($__DIR__.'/lib/gantry/gantry.php');
global $mainframe;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $gantry->language; ?>" lang="<?php echo $gantry->language;?>" >
 <head>
 <?php $gantry->displayHead(); ?>
</head>
<body <?php echo $gantry->displayBodyTag(); ?>>

<div id="rt-header">
	<div class="rt-container">
		<div class="rt-block">
			<img src="<?php print $gantry->templateUrl ?>/images/logo/logo.png" />
			
	    </div>
	<div class="clear"></div>
	</div>
</div>

<div id="rt-utility" class="rounded">
	<div class="rt-container cleafix">
		<div class="rt-block">
			<jdoc:include type="message" />
		</div>
	</div>
</div>

<div id="rt-maintop">
	<div class="rt-container clearfix">
		<div class="rt-grid-8 rt-alpha">
			<div class="rt-block">
				<div class="standard-module rounded">
					<h2 class="module-title"><?php echo $mainframe->getCfg('sitename'); ?></h2>
					<div class="module-content">
						<p><?php echo $mainframe->getCfg('offline_message'); ?></p>
					</div>
				</div>
			</div>
		</div>
		
		<div class="rt-grid-4 rt-beta">
			<div class="rt-block">
				<div class="standard-module rounded">
					<h2 class="module-title"><?php print JText::_('LOGIN') ?></h2>
					<div class="module-content">
					
						<form action="index.php" method="post" name="login" id="form-login" class="form-stacked">
						
						<div class="clearfix">
							<label for="modlgn_username"><?php echo JText::_('Username') ?></label>
							<div class="input">
								<input id="modlgn_username" type="text" name="username" class="xxlarge" alt="username" size="18" />
							</div>
						</div>
						
						<div class="clearfix">
							<label for="modlgn_passwd"><?php echo JText::_('Password') ?></label>
							<div class="input">
								<input id="modlgn_passwd" type="password" name="passwd" class="xxlarge" size="18" alt="password" />
							</div>
						</div>
						
						<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
						<div class="clearfix">
							<label class="remember">
								<input type="checkbox" name="remember" value="yes" alt="<?php echo JText::_('Remember me'); ?>" /> 
								<?php echo JText::_('Remember me'); ?>
							</label>
						</div>
						<?php endif; ?>
						
						<div class="clearfix">
							<input type="submit" name="Submit" class="btn primary" value="<?php echo JText::_('LOGIN') ?>" />
						</div>
							
						
						<input type="hidden" name="option" value="com_user" />
						<input type="hidden" name="task" value="login" />
						<input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>" />
						<?php echo JHTML::_( 'form.token' ); ?>
						</form>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


</body>
</html>
<?php
$gantry->finalize();
?>