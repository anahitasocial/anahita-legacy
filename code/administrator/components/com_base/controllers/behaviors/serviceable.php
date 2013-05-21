<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Serviceable Behavior.
 *  
 * Specializes some of the service methods
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerBehaviorServiceable extends LibBaseControllerBehaviorServiceable
{
    /**
     * Return the item
     *
     * @param KCommandContext $context Context parameter
     *
     * @return mixed
     */
    protected function _actionRead(KCommandContext $context)
    {
        //create an empty entity for the form layout;
        if ( !$this->getItem() ) {
            $this->setItem($this->getRepository()->getEntity()->reset());
        }
        return $this->getItem();
    }
    
    /**
     * Saves/Add an entity and then redirects
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainEntitysetAbstract
     */
    protected function _actionPost($context)
    {
        if ( $context->action == 'save' )
            $context->response->setRedirect('option=com_'.$this->getIdentifier()->package.'&view='.KInflector::pluralize($this->getIdentifier()->name));
    
        $data = $context->data;
    
        //searches for any \w+_id pattern and then set a relationship
        //accordingly
        //Should be moved to a behavior
        foreach($data as $key => $value)
        {
            if ( strpos($key,'_id') )
            {
                $key = str_replace('_id', '', $key);
                $this->getState()->$key = $this->getRepository($key)->fetch($value);
            }
        }
    
        if ( $this->getItem() )
            $this->execute('edit', $context);
        else {
            $this->execute('add',  $context);
        }
    
        return $this->getItem();
    }
    
    /**
     * Create a new entity
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionAdd($context)
    {
        $entity = parent::_actionAdd($context);       
        $data 	= $context->data;    
        if ( $entity->isDictionariable() && $data->meta )
        {
            foreach($data->meta as $key => $value) {
                $entity->setValue($key, $value);
            }
        }
        return $entity;
    }
    
    /**
     * Edits an entity
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionEdit($context)
    {
        $entity = parent::_actionEdit($context);
        $data   = $context->data;    
        if ( $entity->isDictionariable() && $data->meta )
        {
            foreach($data->meta as $key => $value) {
                $entity->setValue($key, $value);
            }
        }
    
        return $entity;
    }
    
    /**
     * Return a set of entities
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainEntitysetAbstract
     */
    protected function _actionBrowse($context)
    {
        $data  = $context->data;
    
        $context->append(array(
                'query' => $this->getRepository()->getQuery()
        ));
    
        $query = $context->query;
    
        $query->order($this->sort, $this->direction)
        ->limit($this->limit, $this->limitstart);
    
        $query->keyword = $this->search;
    
        if ( $this->getRepository()->hasBehavior('parentable') ) {
            if ( $this->getState()->parent ) {
                $query->parent($this->getState()->parent);
            } elseif ($this->pid == -1) {
                $query->parent(null);
            }
        }
    
        return $this->getState()->setList($query->toEntitySet())->getList();
    }    
}