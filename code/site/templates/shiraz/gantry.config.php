<?php
/**
 * @package 	Gantry Template Framework - RocketTheme
 * @version 	3.1.4 November 12, 2010
 * @author 		RocketTheme http://www.rockettheme.com
 * @copyright	Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license 	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('JPATH_BASE') or die();

$gantry_config_mapping = array(
    'belatedPNG' => 'belatedPNG'
);

$gantry_presets = array(
    'presets' => array(
        'preset1' => array(
            'name' 			=> 'Light',
            'cssstyle'		=> 'style1'
        ),
        
        'preset2' => array(
            'name' 			=> 'Dark',
            'cssstyle' 		=> 'style2'
        ),
        
        'preset3' => array(
            'name' 			=> 'Custom',
            'cssstyle' 		=> 'style3'
        )
    )
);

$gantry_default_mainbodyschemas = array(
    12 => array(
        1 => array('mb'=>12),
        2 => array('mb'=>8, 'sb'=>4),
        3 => array('sa'=>2, 'mb'=>6, 'sb'=>4)
    )
);

$gantry_default_pushpullschemas = array(
	'mb12' 			=> array(''),
	'mb8-sb4'		=> array('', ''),
	'sa2-mb6-sb4'	=> array('rt-push-2', 'rt-pull-6', '')
);


$gantry_default_mainbodyschemascombos = array(
	12 => array(
	    1 => array(
	            array('mb'=>12)
	    ),
	    2 => array(
	            array('mb'=>8, 'sb'=>4)
	    ),
	    3 => array(
	            array('sa'=>2, 'mb'=>6, 'sb'=>4)
	    )
	)
);
