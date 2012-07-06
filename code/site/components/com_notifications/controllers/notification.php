<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Notification Controller
 *
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComNotificationsControllerNotification extends ComBaseControllerService
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
        
    }
        
   /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(			
			'behaviors'	=> array('ownable'),
            'request'   => array('oid'=>'viewer')
		));
	
		parent::_initialize($config);
	}
	
    /**
     * Return the count of new notifications
     * 
     * @return string
     */
    protected function _actionGetcount(KCommandContext $context)
    {
        $data  = $context->data;
        $count = $data->actor->numOfNewNotifications();
        return $this->getView()->new_notifications($count)->display();
    }
    
	/**
	 * Return a set of notification objects
	 * 
	 * @param  KCommandContext $context Context parameter 
     * 
	 * @return AnDomainEntitysetDefault
	 */
	protected function _actionBrowse($context)
	{
		$data	= $context->data;
         
        $data->actor->resetNotifications();
              
        if ( $data->actor->eql(get_viewer()) ) 
            $title = JText::_('COM-NOTIFICATIONS-ACTORBAR-YOUR-NOTIFICATIONS');  
        else 
            $title = sprintf(JText::_('COM-NOTIFICATIONS-ACTORBAR-ACTOR-NOTIFICATIONS'), $data->actor->name);
        
        $this->getToolbar('actorbar')->setTitle($title);
                
		$context->query = $data->actor->getNotifications()->getQuery();
        
        $set = parent::_actionBrowse($context)->order('createdOn','DESC');
          
        if ( $this->layout != 'popover' ) {
            $set->limit(0);
        }
        
        //only zero the notifications if the viewer is the same as the 
        //actor. prevents from admin zeroing others notifications
        if ( $set->count() > 0 && get_viewer()->eql($data->actor) ) 
        {
            //set the number of notification, since it's going to be 
            //reset by the time it gets to the mod_viewer 
            KService::setConfig('mod://site/viewer.html', array('data'=>array('num_notifications'=>$data->actor->numOfNewNotifications())));            
            $this->registerCallback('after.get', array($data->actor,'viewedNotifications'), $set->toArray());
        }
        
		return $set;
	}
	
	/**
	 * Fake deleting a notification by removing the owner from the notification owners
	 * 
	 * @param  KCommandContext $context Context parameter
     * 
	 * @return AnDomainEntityAbstract
	 */
	protected function _actionDelete($context)
	{
        $data = $context->data;
        $data->actor->removeNotification($data->entity);
		return $data->entity;
	}
    
    /**
     * Checks if this controller can be executed by the viewer
     * 
     * @param KCommandContext $context The CommandChain Context
     *
     * @return boolean
     */
    public function canExecute(KCommandContext $context)
    {
        $data = $context->data;
        
        if ( !$data->actor )
            return false;
        
        if ( !$data->actor->isNotifiable() )
            return false;
        
        if ( $data->actor->authorize('access') === false ) {
            return false;
        }
        
        if ( $data->actor->authorize('administration') === false )
            return false;
            
        return $this->__call('canExecute', array($context));    
    }
}