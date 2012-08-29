<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Controller State
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseControllerState extends KConfig
{ 
    /**
     * The entity/entityset
     * 
     * @return AnDomainEntityAbstract|AnDomainEntityset 
     */ 
    protected $_item;
    
    /**
     * List resource
     * 
     * @return AnDomainEntityset
     */
    protected $_list;
    
    /**
     * Set the state
     * 
     * @param array $data An array of data
     * 
     * @return LibBaseControllerData
     */
    public function setData(array $data)
    {
        foreach($data as $key => $value)
        {
            $this->$key = $value;   
        }
        return $this;
    }
    
    /**
     * Set the item
     * 
     * @param mixed $item The item 
     * 
     * @return LibBaseControllerData
     */
    public function setItem($item)
    {
        $this->_item = $item;
        return $this;
    }
    
    /**
     * Return the item
     * 
     * @return mixed
     */
    public function getItem()
    {
        return $this->_item;
    }
    
    /**
     * Set the list
     * 
     * @param mixed List items
     * 
     * @return LibBaseControllerData
     */
    public function setList($list)
    {
        $this->_list = $list;
        return $this;
    }
    
    /**
     * Return list
     * 
     * @return mixed
     */
    public function getList()
    {
        return $this->_list;
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