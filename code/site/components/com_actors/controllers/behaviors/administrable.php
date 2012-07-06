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
 * Administrable Behavior
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsControllerBehaviorAdministrable extends KControllerBehaviorAbstract
{	
	/**
	 * Remove admin
	 * 
	 * @param KCommandContext $context Context parameter
     * 
	 * @return void
	 */
	protected function _actionRemoveadmin(KCommandContext $context)
	{
        $data = $context->data;
        if ( $data->admin )
		  $data->entity->removeAdministrator($data->admin);
	}

	/**
	 * Add Admin
	 * 
	 * @param KCommandContext $context Context parameter
     * 
	 * @return void
	 */
	protected function _actionAddadmin(KCommandContext $context)
	{
        $data = $context->data;
		if ( $data->admin )
		    $data->entity->addAdministrator($data->admin);
	}

	/**
	 * Get Candidates
	 * 
	 * @param KCommandContext $context Context parameter
	 * @return void
	 */
	protected function _actionGetcandidates(KCommandContext $context)
	{		
		if ( $this->format != 'html' )
		{
			$data = $context->data;
			$canditates = $data->entity->getAdminCanditates();
			$canditates->keyword($this->value)->limit(10);
			$people = array();
		    foreach($canditates as $key => $person) {
				$people[$key] = array('id'=>$person->id, 'value'=>$person->name);
			}
			$context->data = $people;
			return $this->render($context);
		}
	}
		
	/**
	 * Get settings
	 * 
	 * @param KCommandContext $context Context parameter
     * 
	 * @return void
	 */
	protected function _actionGetsettings(KCommandContext $context)
	{
		$data = $context->data;
			
		$this->getToolbar('actorbar')->setActor($data->entity);
		$this->getToolbar('actorbar')->setTitle(sprintf(JText::_('COM-ACTORS-PROFILE-HEADER-EDIT'), $data->entity->name));
		
		
		$data->apps = $this->getService('repos:apps.app')->getQuery()
			->actor($data->entity)
			->access(ComAppsDomainEntityApp::ACCESS_OPTIONAL)
			->fetchSet();
        
        $dispatcher = $this->getService('anahita:event.dispatcher');
        
        $data->apps->registerEventDispatcher($dispatcher);
        
        $dispatcher->addEventListener('onSettingDisplay', $this->_mixer);              
	}
    
	/**
	 * Add App
	 *
	 * @param KCommandContext $context Context parameter
     * 
     * @return void
	 */
	protected function _actionAddapp(KCommandContext $context)
	{
		$data 	   = $context->data;
		$app	   = $this->getService('repos:apps.app')->fetch(array('component'=>$data->app));
		if ( $app && $app->authorize('install', array('actor'=>$data->entity))) {
		    $app->addToProfile($data->entity);
		}
	}
	
	/**
	 * Remove App
	 *
	 * @param KCommandContext $context Context parameter
     * 
     * @return void
	 */
	protected function _actionRemoveapp(KCommandContext $context)
	{
		$data 	   = $context->data;
		$app	   = $this->getService('repos:apps.app')->fetch(array('component'=>$data->app));
		if ( $app ) {
			$app->removeFromProfile($data->entity);
		}		
	} 
    
    /**
     * Confirm a requester
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    protected function _actionConfirmrequester(KCommandContext $context)
    {
        $data      = $context->data;
        
        //add the requester as a follower
        //the rest is take care of
        $data->entity->addFollower($data->requester);
    }
    
    /**
     * Ignores a requester
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    protected function _actionIgnorerequester(KCommandContext $context)
    {
        $data      = $context->data;
        
        //add the requester as a follower
        //the rest is take care of
        $data->entity->removeRequester($data->requester);
    }
          
}