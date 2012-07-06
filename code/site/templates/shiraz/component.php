<?php
/**
 * @package Gantry Template Framework - RocketTheme
 * @version 3.1.4 November 12, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

// load and inititialize gantry class
require_once($__DIR__.'/lib/gantry/gantry.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print$gantry->language; ?>" lang="<?php print$gantry->language;?>" >
	<head>			
    	<link rel="shortcut icon" href="<?php print $gantry->templateUrl ?>/images/<?php print $gantry->get('favicon-file') ?>" />
        <?php $gantry->displayHead() ?>
    	<?php if ( JDEBUG ) : ?>
    	<?php $this->renderHelper('less.compile', 'templates/shiraz/css/'.$gantry->get('cssstyle').'.less')?>
    	<?php endif;?>
    	<style>.filterable {visibility:hidden}</style>      
  	 </head>
    <body id="tmpl-component" <?php print $gantry->displayBodyTag(); ?>>
    	<jdoc:include type="script" />
	    <?php print$gantry->displayMainbody('mainbody','sidebar','standard','standard','standard','standard','standard'); ?>
        <script>window.applyBehaviors('#rt-main')</script>
	</body>
</html>
<?php $gantry->finalize(); ?>