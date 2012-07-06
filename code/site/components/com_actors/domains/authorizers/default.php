<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Default Actor Authorizer
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsDomainAuthorizerDefault extends LibBaseDomainAuthorizerDefault
{
	/**
	 * Check if the actor authorize adminisrating it
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */	
	protected function _authorizeAdministration(KCommandContext $context)
	{
	    $ret = false;
	    
	    if ( $this->_entity->authorize('access', $context) )
	    {
            if ( $this->_viewer->isAdministrator() )
            {
                $ret = $this->_viewer->administrator($this->_entity);
            }
	        
	        if ( $context->strict !== true )
	        {
	            $ret = $ret || $this->_viewer->admin();
	        }
	    }
		
		return $ret;
		
	}
	
    /**
     * Check if the viewer can set certain privacy value
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */     
    protected function _authorizeSetPrivacyValue(KCommandContext $context)
    {
        $value = $context->value;
        
        if ( $this->_entity->authorize('administration') ) {        
            return true;    
        }
        
        switch($value) 
        {
            case LibBaseDomainBehaviorPrivatable::GUEST :
            case LibBaseDomainBehaviorPrivatable::REG :
                return true;
            case LibBaseDomainBehaviorPrivatable::FOLLOWER :                
                return $this->_entity->isFollowable() && $this->_entity->leading($this->_viewer);
            default :
                return $this->_entity->authorize('administration');            
        }
        
        
    }
        
	/**
	 * Check if the actor authorize viewing a resource
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */		
	protected function _authorizeAccess(KCommandContext $context)
	{
        //if entity is not privatable then it doesn't have access to allow method
        if ( !$this->_entity->isPrivatable() )
            return true;
            
		$resource = $context->resource;
		
		if ( empty($resource) ) {
			$resource = 'access';
		}
				
		if ( $resource == 'access' ) 
        {
            $ret = true;
            
            if  ( $this->_entity->isFollowable() && $this->_entity->blocking($this->_viewer) )
                $ret = false;                
			else
                $ret = $this->_entity->allows($this->_viewer, $resource);
                
            return $ret;
		} 
		else if ( $resource != 'access'  ) 
		{
			if ( $this->_entity->allows($this->_viewer, 'access') === false )
				return false;
		}
		
		if ( strpos($resource,':') === false ) {
			$access = $this->_entity->component.':access:'.$resource;
		} else {
			$parts = explode(':', $resource);
			$component = array_shift($parts);
			array_unshift($parts, 'view');
			array_unshift($parts, $component);
			$access = implode($parts, ':');
		}
		
		return $this->_entity->allows($this->_viewer, $access);
	}
	
	 /**
	 * Check if an actor authorizes publishing a medium node for it
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */	
	protected function _authorizePublish(KCommandContext $context)
	{
		$resource = $context->resource;
		
		if ( $this->_entity->authorize('access', $context) )
		{
		    if ( strpos($resource,':') === false )
		    {
		        $access = $this->_entity->component.':publish:'.$resource;
		    }
		    else
		    {
		        $parts = explode(':', $resource);
		        $component = array_shift($parts);
		        
		        $app = $this->getService('repos:apps.app')->fetch(array('component'=>$component));
		        //check if it's a social app then if it's enabled		        
		        if ( $app && !$app->authorize('publish', array('actor'=>$this->_entity)))  {
		            return false;
		        }
		         
		        array_unshift($parts, 'publish');
		        array_unshift($parts, $component);
		        $access = implode($parts, ':');
		    }
		    
		    return $this->_entity->allows($this->_viewer, $access);		    
		}
		else return false;		    
	}
	
	/**
	 * If true then owner's name is visiable to the viewer, if not the default name is 
	 * displayed
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */	
	protected function _authorizeFollower(KCommandContext $context)
	{                
        //viewer can only follow actor if and only if
        //viewer is leadable and actor is followable           
        if ( $this->_entity->isFollowable() && !$this->_viewer->isLeadable() )
            return null;
                    
        if ( $this->_viewer->eql($this->_entity) )
            return false;
            
        if ( is_guest($this->_viewer) )
            return false;
            
	    if ( !$this->_entity->authorize('access', $context) )
	    {            
	        if ( $this->_entity->isLeadable() && $this->_entity->following($this->_viewer) )
	            return true;
	        else
	            return false;
	    }

        //if the viewer is blocking the entity, then it can not follow
        //the entity
		if ( $this->_viewer->isFollowable() && $this->_viewer->blocking($this->_entity) )
			return false;
		
		return true;
	}
     
    /**
     * Return if the viewer can request to follow the actor
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */ 
    protected function _authorizeRequester(KCommandContext $context)
    {
        //viewer can only follow actor if and only if
        //viewer is leadable and actor is followable           
        if ( $this->_entity->isFollowable() && !$this->_viewer->isLeadable() )
            return null;
             
        if ( !$this->_entity->allowFollowRequest )
            return false; 
                        
        if ( $this->_viewer->eql($this->_entity) )
            return false;
            
        if ( is_guest($this->_viewer) )
            return false;

        //cant' send a requet if already following
        if ( $this->_viewer->following($this->_entity) )
            return false;
           
        //can't send a request if the viewer can follow
         if  ($this->_entity->authorize('follower', array('viewer'=>$this->_viewer)))
            return false;
         
        //cant' send a requet if already requested
        if ( $this->_entity->requested($this->_viewer) )
            return false;
            
        //if the viewer is blocking the entity, then it can not follow
        //the entity
        if ( $this->_viewer->isFollowable() && $this->_viewer->blocking($this->_entity) )
            return false;
        
        return true;
    } 
             
    /**
     * Checks whether the viewer can unfollow the actor
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */ 
    protected function _authorizeUnfollow(KCommandContext $context)
    {
        //if the viewer is not following then return false;
        //Riddle : HOW can you unfollow an actor that you are not following
        if ( !$this->_viewer->following($this->_entity) )
            return false;
                    
        //if entity is adminitrable and the viewer is an admin
        //and there are only one admin. 
        //then the viewer can't unfollow
        if ( $this->_entity->isAdministrable() 
                && $this->_entity->administratorIds->offsetExists($this->_viewer->id) ) 
        {            
            return $this->_entity->administratorIds->count() >= 2;
        }
            
        return true;
    }
            
    /**
     * Return if the viewer can remove an admin of an actor. It returns true
     * if an actor has at least two actors 
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */ 
    protected function _authorizeRemoveAdmin(KCommandContext $context)
    {
        if ( $this->_entity->isAdministrable() ) {
            return $this->_entity->administratorIds->count() >= 2;             
        }
        
        return false;
    }
    
    /**
     * Check if a node authroize being subscribed too
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */
    protected function _authorizeSubscribe($context)
    {
        $entity = $this->_entity;
        
        if ( is_guest($this->_viewer) )
            return false;
    
        if ( !$entity->isSubscribable() )
            return false;
        
        return $this->_viewer->following($entity);
    }
    
	/**
	 * If true then owner's name is visiable to the viewer, if not the default name is 
	 * displayed
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */	
	protected function _authorizeBlocker(KCommandContext $context)
	{
        //viewer can only block actor from following them if and only if
        //actor is leadable (can follow ) and viewer is followable        
        if ( !$this->_entity->isLeadable() || !$this->_viewer->isFollowable() ) {
            return false;    
        }

        if ( is_guest($this->_viewer) )
            return false;
                           
        if ( $this->_viewer->eql($this->_entity) )
            return false;
         
        //if entity is administrable and the viewer is one of the admins
        //then it can not be blocked 
        if ( $this->_viewer->isAdministrable() 
                && $this->_viewer->administratorIds->offsetExists($this->_entity->id) ) 
        {
            return false;
        }
                 
//         //if the entity is   
//        if ( $this->_entity->following($this->_viewer) )
//            return true;
//            
//	    if ( !$this->_entity->authorize('access', $context) )
//	        return false;
		
		return true;
	 }
}

?>