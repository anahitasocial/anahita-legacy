<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Loads a javascript langauge file 
 * 
 * NOTE Expermimental and subject to change
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseTemplateHelperLanguage extends KTemplateHelperAbstract 
{
    /**
     * Loads an aray of javascript language
     * 
     * @params array $langs Array of language files
     * 
     * @return void
     */
    public function load($langs)
    {
        //force array
        settype($langs, 'array');
        
        $scripts  = '';               
        $tag      = JFactory::getLanguage()->getTag();
        $base     = JLanguage::getLanguagePath( JPATH_ROOT, $tag);
        foreach($langs as $lang)
        {
            $path        = $base.'/'.$tag.'.'.$lang.'.js';
            if ( is_readable($path) )
            {
                $content     = '{'.file_get_contents($path).'}';
                $scripts    .=  '<script type="text/language">'.$content.'</script>';                
            }            
        }
        return  $scripts;             
    }
}