<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * NOTE : Experimental Class. Will be changed in the future.
 * 
 * Provides the ability to chunify a HTML document.  
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseTemplateHelperString extends KObject implements KServiceInstantiatable
{
    /**
     * Array of strings
     * 
     * @return array
     */
    protected $_strings = array();
    
    /**
     * Force creation of a singleton
     *
     * @param KConfigInterface  $config    An optional KConfig object with configuration options
     * @param KServiceInterface $container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier))
        {
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }
    
        return $container->get($config->service_identifier);
    }
    
    /**
     * Register
     * 
     * @param $object The object to register
     * 
     * @return string
     */
    public function register($object)
    {
        $handle     = $object->getHandle();
        $this->_strings[$handle] = $object;
        return $handle;
    }
    
    /**
     * Chunkify a string into peices seperated by handle
     * 
     * @return array
     */
    public function chunkify($string)
    {
       $chunks = array();
       
       foreach($this->_strings as $handle => $object)
       {
           $pos = strpos($string, $handle);            
           if ( $pos !== false ) 
           {
                $chunks[] = substr($string, 0, $pos);
                $chunks[] = $object;
                $string   = substr($string, $pos + strlen($handle), -1);
           }
       }
      
       $chunks[] = $string;
       
       return $chunks;
    }
}