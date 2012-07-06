<?php
/**
 * @package     gantry
 * @subpackage  features
 * @version		3.1.4 November 12, 2010
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');
/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureCopyright extends GantryFeature {
    var $_feature_name = 'copyright';

	function render($position="") {
	    ob_start();
	    ?>
		<div class="clear"></div>
		<div class="rt-block">
			<div id="copyright-message">
				<p>
				Shiraz template is developed by <a href="http://www.anahitapolis.com" target="_blank">Anahitapolis.com</a> for the Anahita™ Social Networking Platform and Framework.
				</p>
				<?php if( $this->get('text') ): ?>
				<p><?php echo $this->get('text'); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	    return ob_get_clean();
	}
}