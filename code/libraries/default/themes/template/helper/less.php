<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Themes
 * @subpackage Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

require_once 'lessc.inc.php';

/**
 * Less Compiler Template Helper
 * 
 * @category   Anahita
 * @package    Lib_Themes
 * @subpackage Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibThemesTemplateHelperLess extends KTemplateHelperAbstract
{
	/**
	 * Compiles a less css file. The the compiler will create a css file output
	 * 
	 * @param string $path   The path to the css 
	 * @param array  $config Array of less compile configuration
	 * 
	 * @return void
	 */
	public function compile($path, $config = array())
	{
		$path = JPATH_ROOT.'/'.$path;
		$config = new KConfig($config);
		$config->append(array(
			'auto_compile' 	=> true,
			'output'		=> dirname($path).'/'.str_replace('.less','.css', basename($path))
		));
		
		$out = $config->output;
		
		if ( $config->auto_compile ) {
			$cache_file 	 = JFactory::getConfig()->getValue('tmp_path').'/less-'.md5($path);

			if ( file_exists($cache_file) ) {
				$cache = unserialize(file_get_contents($cache_file));
			} else
				$cache = $path;
				
			$new_cache = lessc::cexecute($cache);
			if (!is_array($cache) || $new_cache['updated'] > $cache['updated']) {
    			file_put_contents($cache_file, serialize($new_cache));
    			file_put_contents($out, 	   $new_cache['compiled']);
  			}
		}
		else lessc::ccompile($path, $out);
	}
}