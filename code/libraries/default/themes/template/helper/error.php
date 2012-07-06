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

/**
 * Error handler template helper
 * 
 * @category   Anahita
 * @package    Lib_Themes
 * @subpackage Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibThemesTemplateHelperError extends KTemplateHelperAbstract
{    
    /**
     * Renders an error message using the JErrro Object
     * 
     * @param JError $error The error object
     * 
     * @return string
     */
    public function render($error)
    {
        $content = (string)$this->loadError($error);
        
        $data    = $this->getTemplate()->getData();
        
        if (isset($data['backtrace']) && JDEBUG )
             $content .= $data['backtrace'];
        
        $this->getTemplate()->getBuffer()->component = $content;
        
        //load the template   
        return $this->getTemplate()->loadTemplate('index');   
    }
    
    /**
     * Loads the error layout
     * 
     * @param JError $error The error object
     * 
     * @return string
     */    
    public function loadError($error)
    {
        $file = 'error_default';
        
        if ( isset($error->code) ) 
        {     
            if ( $this->getTemplate()->findPath('error_'.$error->code.'.php') ) {
                $file = 'error_'.$error->code;
            }            
        } 
        
        return $this->getTemplate()->loadTemplate($file);       
    }
}