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
    <body <?php print$gantry->displayBodyTag(); ?>>
        <jdoc:include type="script" />
        <?php /** Begin Menu **/ if ($gantry->countModules('navigation')) : ?>
        <div class="navbar navbar-fixed-top">
        	<div class="navbar-inner">
        		<div class="container">
					<?php $logo = ($gantry->get('brand-logo')) ? 'background: url('.$gantry->templateUrl.DS.'images'.DS.$gantry->get('brand-logo').') no-repeat 10px 10px transparent' : '' ?>
	        		<a class="brand <?php print ($gantry->get('brand-logo')) ? 'brand-logo' : '' ?>" style="<?php print $logo ?>" href="<?php print $gantry->baseUrl ?>">
	        			<?php print $gantry->get('brand-name') ?>
	        		</a>
					<?php print$gantry->displayModules('navigation','basic','basic'); ?>
					<?php if ($gantry->countModules('viewer')) : ?>
					<span class="viewer"><?php print$gantry->displayModules('viewer','basic','basic'); ?></span>
					<?php endif; ?>
        		</div>
        	</div>
        </div>   
        <?php /** End Menu **/ endif; ?>
        
		<?php /** Begin Header **/ if ($gantry->countModules('header')) : ?>
		<div id="rt-header">
			<div class="rt-container">
				<?php print$gantry->displayModules('header','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Header **/ endif; ?>
		
		<?php /** Begin Showcase **/ if ($gantry->countModules('showcase')) : ?>
		<div id="rt-showcase">
			<div class="rt-container">
				<?php print$gantry->displayModules('showcase','standard','basic'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Showcase **/ endif; ?>
		<?php /** Begin Feature **/ if ( $gantry->countModules('feature')) : ?>
		<div id="rt-feature">
			<div class="rt-container">
				<?php print$gantry->displayModules('feature','standard','simple'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Feature **/ endif; ?>
		<?php /** Begin Breadcrumbs **/ if ($gantry->countModules('breadcrumb')) : ?>
		<div id="rt-breadcrumbs">
			<div class="rt-container">
				<?php print$gantry->displayModules('breadcrumb','standard','basic'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Breadcrumbs **/ endif; ?>
		<?php /** Begin Main Top **/ if ($gantry->countModules('maintop')) : ?>
		<div id="rt-maintop">
			<div class="rt-container">
				<?php print$gantry->displayModules('maintop','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Main Top **/ endif; ?>
		<?php /** Begin Utility **/ if ($gantry->countModules('utility')) : ?>
		<div id="rt-utility">
			<div class="rt-container">
				<?php print$gantry->displayModules('utility','standard','basic'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Utility **/ endif; ?>
		<?php /** Begin Main Body **/ ?>
	    <?php print$gantry->displayMainbody('mainbody','sidebar','standard','standard','standard','standard','standard'); ?>
        <script>window.applyBehaviors('#rt-main')</script>
		<?php /** End Main Body **/ ?>
		<?php /** Begin Main Bottom **/ if ($gantry->countModules('mainbottom')) : ?>
		<div id="rt-mainbottom">
			<div class="rt-container">
				<?php print$gantry->displayModules('mainbottom','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Main Bottom **/ endif; ?>
		<?php /** Begin Bottom **/ if ($gantry->countModules('bottom')) : ?>
		<div id="rt-bottom">
			<div class="rt-container">
				<?php print$gantry->displayModules('bottom','standard','basic'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Bottom **/ endif; ?>
		<?php /** Begin Footer **/ if ($gantry->countModules('footer')) : ?>
		<div id="rt-footer">
			<div class="rt-container">
				<?php print$gantry->displayModules('footer','standard','simple'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Footer **/ endif; ?>
		<?php /** Begin Copyright **/ if ($gantry->countModules('copyright')) : ?>
		<div id="rt-copyright">
			<div class="rt-container">
				<?php print$gantry->displayModules('copyright','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Copyright **/ endif; ?>
		<?php /** Begin Debug **/ if ($gantry->countModules('debug')) : ?>
		<div id="rt-debug">
			<div class="rt-container">
				<?php print$gantry->displayModules('debug','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Debug **/ endif; ?>
		<?php /** Begin Analytics **/ if ($gantry->countModules('analytics')) : ?>
		<?php print$gantry->displayModules('analytics','basic','basic'); ?>
		<?php /** End Analytics **/ endif; ?>
        <?= $this->renderHelper('language.load', 'lib_anahita') ?>
	</body>
</html>
<?php
$gantry->finalize();
?>
