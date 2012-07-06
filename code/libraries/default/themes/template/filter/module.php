<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Themes
 * @subpackage Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Renders module
 * 
 * @category   Anahita
 * @package    Lib_Themes
 * @subpackage Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibThemesTemplateFilterModule extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
    /**
     * Convert the alias
     *
     * @param string
     * @return KTemplateFilterAlias
     */
    public function write(&$text) 
    {
    	$matches  = array();
    	$replaces = array();		
			
    	if( preg_match_all('#<jdoc:include\ type="modules" (.*)\/>#iU', $text, $matches) )
    	{
    		foreach($matches[0] as $i => $match) 
    		{
    			$attrs 	  = $this->_parseAttributes($matches[1][$i]);
    			$modules  = JModuleHelper::getModules($attrs['name']);
				$contents = '';
				foreach ($modules as $module)  {
					if ( !isset($module->attribs) )
						$module->attribs = $attrs;
					$contents .= $this->renderModule($module);
				}
				$replaces[] = $contents;
    		}
		}
		$text = str_replace($matches[0], $replaces, $text);
    }
    
    /**
     * Renders a module
     *
     * @param  object $module
     * @return string
     */
    public function renderModule($module)
    {
    	$content = null;
    	
    	$params	 = $module->attribs;
    	
		if (!is_object($module))
		{
			$title	= isset($params['title']) ? $params['title'] : null;

			$module =& JModuleHelper::getModule($module, $title);

			if (!is_object($module))
			{
				if (is_null($content)) {
					return '';
				} else {
					/**
					 * If module isn't found in the database but data has been pushed in the buffer
					 * we want to render it
					 */
					$tmp = $module;
					$module = new stdClass();
					$module->params = null;
					$module->module = $tmp;
					$module->id = 0;
					$module->user = 0;
				}
			}
		}

		// get the user and configuration object
		$user =& JFactory::getUser();
		$conf =& JFactory::getConfig();

		// set the module content
		if (!is_null($content)) {
			$module->content = $content;
		}

		//get module parameters
		$mod_params = new JParameter( $module->params );

		$contents = '';
		if ($mod_params->get('cache', 0) && $conf->getValue( 'config.caching' ))
		{	
			$cache =& JFactory::getCache( $module->module );

			$cache->setLifeTime( $mod_params->get( 'cache_time', $conf->getValue( 'config.cachetime' ) * 60 ) );
			$cache->setCacheValidation(true);

			$contents =  $cache->get( array('JModuleHelper', 'renderModule'), array( $module, $params ), $module->id. $user->get('aid', 0) );
		} else {
			$contents = JModuleHelper::renderModule($module, $params);
		}

		return $contents;    	
    }
}