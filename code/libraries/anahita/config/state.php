<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Config
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Enhances KConfigState. This class is primiray used as container to be used to hold states
 * of certain objects (controller)
 *
 * @category   Anahita
 * @package    Anahita_Config
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnConfigState extends KConfigState 
{
    /**
     * Set a model state by name
     *
     * @param   string  The key name.
     * @param   mixed   The value for the key
     * @return  void
     */
    public function __set($key, $value)
    {
        if ( $key == 'resource' ) 
        {
            $this->setResource(value);    
        }
        elseif ( !isset($this->$key) ) 
        {            
            $this->insert($key, 'raw', $value);              
        }
                
        return parent::__set($key, $value);
    }

    /**
     * Supports a simple form Fluent Interfaces. Allows you to set states by
     * using the state name as the method name.
     *
     * For example : $model->sort('name')->limit(10)->getList();
     *
     * @param   string  Method name
     * @param   array   Array containing all the arguments for the original call
     * @return  KModelAbstract
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args)
    {
        if( count($args) > 0 ) {
            $this->__set(KInflector::underscore($method), $args[0]);
            return $this;
        }
         
        throw new BadMethodCallException('Call to undefined method :'.$method);
    }
}