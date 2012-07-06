<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Executable Behavior
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsControllerBehaviorExecutable extends LibBaseControllerBehaviorExecutable
{    		
	/**
	 * Authorize Delete. Only if the viewer if is an admin of the actor
	 * 
	 * @param KConfig $data Data
	 * 
	 * @return boolean
	 */
	public function canDelete(KConfig $data)
	{		
		return $data->entity->authorize('administrations');
	}
	
	/**
	 * Authorize Read
	 * 
	 * @param KConfig $data Data
	 * 
	 * @return boolean
	 */
	public function canRead(KConfig $data)
	{	    
	    if ( $this->layout == 'add' )
	        return $this->canAdd($data);	        
	    	    
	    return true;
	}

	/**
	 * Authorize Edit
	 *
	 * @param KConfig $data Data
	 * 
	 * @return boolean
	 */
	public function canEdit(KConfig $data)
	{		
		return $data->entity->authorize('administration');
	}
		
	/**
	 * Authorize Add
	 * 
	 * @param KConfig $data Data
	 * 
	 * @return boolean
	 */
	public function canAdd(KConfig $data)
	{
	    $app = $this->getService('repos:apps.app')->fetch(array('component'=>'com_'.$this->getIdentifier()->package));
	    if ( $app ) 
	        return $app->authorize('publish');
	        
		return false;
	}
	
    /**
     * Authorize following the actor
     *
     * @param KConfig $data Data
     * 
     * @return boolean
     */
    public function canAddrequester(KConfig $data)
    {
        if ( !$data->actor )
            return false;
            
        if ( !$data->entity )
            return false;
        
        return $data->entity->authorize('requester', array('viewer'=>$data->actor));        
    }
        
	/**
	 * Authorize following the actor
	 *
	 * @param KConfig $data Data
	 * 
	 * @return boolean
	 */
	public function canAddfollower(KConfig $data)
	{
        if ( !$data->actor )
            return false;
            
	    if ( !$data->entity )
            return false;
	    
	    return $data->entity->authorize('follower', array('viewer'=>$data->actor));	    
	}
    
    /**
     * Authorize unfollowing the actor
     *
     * @param KConfig $data Data
     * 
     * @return boolean
     */
    public function canDeletefollower(KConfig $data)
    {
        if ( !$data->actor )
            return false;
            
        if ( !$data->entity )
            return false;
        
        return $data->entity->authorize('unfollow', array('viewer'=>$data->actor));        
    }

    /**
     * Authorize blocking the actor
     *
     * @param KConfig $data Data
     * 
     * @return boolean
     */
    public function canAddblocked(KConfig $data)
    {
        if ( !$data->actor )
            return false;
            
        if ( !$data->entity )
            return false;
        
        return $data->actor->authorize('blocker', array('viewer'=>$data->entity));     
    }    
    
    /**
     * Return if the admin can be removed
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */    
    public function canRemoveadmin(KConfig $data)
    {    
        $data->admin = $this->getService('repos://site/people.person')->fetch($data->adminid);        
        return $data->entity->authorize('remove.admin', array('admin'=>$data->admin));
    }
    
    /**
     * Return if the requester can be confirmed
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */    
    public function canConfirmrequester(KConfig $data)
    {                 
        $data->requester = $data->entity->requesters->fetch($data->requester);
        return !is_null($data->requester);
    }

    /**
     * Return if the requester can be confirmed
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */    
    public function canIgnorerequester(KConfig $data)
    {
        return $this->canConfirmrequester($data);
    }    
        
    /**
     * Return if the admin can be removed
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return boolean
     */    
    public function canAddadmin(KConfig $data)
    {
        $data->admin = $data->entity->getAdminCanditates()->id($data->adminid)->fetch();
        return !is_null($data->admin);   
    }
        
    /**
     * If the viewer has been blocked by an actor then don't bring up the actor
     * 
     * @param KCommandContext $context The CommandChain Context
     * 
     * @return boolean
     */
    public function canExecute(KcommandContext $context)
    {
        $data = $context->data;
        
        if ( $data->entity && $data->entity->blocking(get_viewer()) )
            return false;
        
        $action = '_action'.ucfirst($context->action);
        
        //if the action is an admin action then check if the
        //viewer is an admin
        if ( $this->isAdministrable() ) 
        {
            $methods = $this->getBehavior('administrable')->getMethods();
            
            if ( in_array($action, $methods) ) {              
                if ( $this->canAdministare($context->data) === false ) {
                    return false;
                }
            }
        }
             
        return parent::canExecute($context);
    }        
    
    /**
     * Return if a the viewer can administare
     * 
     * @param KConfig $data Data
     * 
     * @return boolean
     */     
    public function canAdministare(KConfig $data)
    {
        if ( !$data->entity ) {
            return false;
        }
                
        return $data->entity->authorize('administration');
    }
}