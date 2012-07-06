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
 * Followable Behavior
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsControllerBehaviorFollowable extends KControllerBehaviorAbstract
{
    /** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */ 
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $config->mixer->registerCallback(
            array('before.deletefollower','before.addfollower',
                  'before.addrequester','before.deleterequester',
                  'before.addblocked','before.deleteblocked'), 
                  array($this, 'getActor'));
                  
        $config->mixer->registerActionAlias('follow',  'addfollower');
        
        $config->mixer->registerActionAlias('unfollow','deletefollower');           
    }
    
    /**
     * Add a set of actors to the owners list of requester.
     * 
     * @param KCommandContext $context Context Parameter
     * 
     * @return AnDomainEntityAbstract The actor
     */
    protected function _actionAddrequester(KCommandContext $context)
    {
        $data = $context->data;
        $data->entity->addRequester($data->actor);
        $this->createNotification(array('subject'=>$data->actor,'target'=>$data->entity,'name'=>'actor_request'));
        return $data->entity;
    }
    
    /**
     * Add a set of actors to the owners list of requester.
     * 
     * @param KCommandContext $context Context Parameter
     * 
     * @return AnDomainEntityAbstract The actor
     */
    protected function _actionDeleterequester(KCommandContext $context)
    {
        $data = $context->data;
        $data->entity->removeRequester($data->actor);
        return $data->entity;
    }    
            
	/**
	 * Add a set of actors to the owners list of followers. 
	 * 
	 * @param KCommandContext $context Context Parameter
	 * 
	 * @return AnDomainEntityAbstract The actor
	 */
	protected function _actionAddfollower(KCommandContext $context)
	{			
		$data = $context->data;
		
		if ( !$data->entity->leading( $data->actor ) )
		{
		    $data->edge = $data->entity->addFollower( $data->actor );
		    
		    $story = $this->createStory(array(
		            'name' 		=> 'actor_follow',
		            'subject'	=> $data->actor,
		            'owner'		=> $data->actor,
		            'target'	=> $data->entity
		    ));
		    
		    //if the entity is not an adiminstrable actor (person)
		    $this->createNotification(array('subject'=>$data->actor, 'target'=>$data->entity,'name'=>'actor_follow'));
		}
			
		return $data->entity;
	}
		
	/**
	 * Add a person to the. The data passed is set my the receiver controller::getCommandChain()::getContext()::data
	 * 
	 * @param KCommandContext $context Context Parameter
	 * 
	 * @return AnDomainEntityAbstract The actor
	 */
	protected function _actionDeletefollower(KCommandContext $context)
	{
		$data = $context->data;
		$data->entity->removeFollower( $data->actor );
		return $data->entity;
	}
    
    
    /**
     * The viewers blocks the actor
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionAddblocked(KCommandContext $context)
    {
        $data = $context->data;
        $data->entity->addBlocked($data->actor);        
        return $data->entity;
    }
    
    /**
     * The viewers unblocks the actor
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return AnDomainEntityAbstract
     */
    protected function _actionDeleteblocked($context)
    {
        $data = $context->data;
        $data->entity->removeBlocked($data->actor);        
        return $data->entity;
    }        
    
    /**
     * Set the subejct before perform graph actions
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    public function getActor(KCommandContext $context)
    {
        $data = $context->data;
       
        if ( $data->actor ) 
        {
            $ret = $this->getService('repos:actors.actor')->fetch($data->actor);
        }
        else 
            $ret = get_viewer();
       
        $data->actor = $ret;
        
        return $data->actor;
    }    
}
