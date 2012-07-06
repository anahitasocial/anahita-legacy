<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Medium
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
 * @package    Com_Medium
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComMediumControllerBehaviorExecutable extends LibBaseControllerBehaviorExecutable
{
	/**
	 * Authorize Browse
	 * 
	 * @param KConfig $data Data
	 * 
	 * @return boolean
	 */
	public function canBrowse(KConfig $data)
	{		    		
		$viewer = get_viewer();
		if ( $this->isOwnable() && $data->actor ) 
		{
			//a viewer can't see  ownable items coming from another actor's leaders
			if ( $this->filter == 'leaders' ) {
				if ( $viewer->id != $data->actor->id )
					return false;
			}
		}
		return true;
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
		$actor		= pick($data->actor, get_viewer());
        
		$resource	= 'com_'.$this->_mixer->getIdentifier()->package.':'.KInflector::pluralize($this->_mixer->getIdentifier()->name);
        
        //if repository is ownable then ask the actor if viewer can publish things
		if ( $this->getRepository()->isOwnable() && in_array($this->layout, array('add', 'edit', 'form','composer')))
			return $actor->authorize('publish', $resource);
				
        if ( !$data->entity )
            return false;
        
        //check if an entiy authorize access       
        return $data->entity->authorize('access');
	}
	
	/**
	 * Authorize if viewer can add
	 *
	 * @param KConfig $data Data
	 * 
	 * @return boolean
	 */
	public function canAdd(KConfig $data)
	{
	    $actor     = $data->actor;
	    
	    if ( $actor )
	    {
	        $resource  = 'com_'.$this->_mixer->getIdentifier()->package.':'.KInflector::pluralize($this->_mixer->getIdentifier()->name);
	        return $actor->authorize('publish',$resource);
	    }
	    
	    return false;	    
	}
	
	/**
	 * Authorize Read
	 * 
	 * @param KConfig $data Data
	 * 
	 * @return boolean
	 */
	public function canEdit(KConfig $data)
	{
		if($data->entity && $data->entity->authorize('edit'))
			return true;
		
		return false;
	}
	
	/**
	 * If an app is not enabled for an actor then don't let the viewer to see it
	 * 
     * @param KCommandContext $context The CommandChain Context
     *
     * @return boolean
	 */
	public function canExecute(KCommandContext $context)
	{
        $data   = $context->data;
	    $viewer = get_viewer();
        
	    if ( KRequest::method() != 'GET' && $viewer->guest() ) {
            return false;
	    }
        
		//check if viewer has access to actor
		if ( $this->isOwnable() && $data->actor )  {
            if ( $data->actor->authorize('access') === false ) 
                return false;			
		}
        
        return parent::canExecute($context);
	}
}