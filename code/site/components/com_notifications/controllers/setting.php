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
 * Notification Setting Controller
 *
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComNotificationsControllerSetting extends ComBaseControllerView
{

    /**
    * Initializes the default configuration for the object
    *
    * Called from {@link __construct()} as a first step of object instantiation.
    *
    * @param KConfig $config An optional KConfig object with configuration options.
    *
    * @return void
    */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'behaviors' => array('ownable','executable')
        ));
        
        parent::_initialize($config);
    }
        
   /**
    * Sets a notification setting 
    *
    * @param KCommandContext $context Context parameter
    *
    * @return void
    */
    protected function _actionPost(KCommandContext $context)
    {
        $data    = $context->data;
        
        $viewer  = get_viewer();
        
        $setting = $this->getService('repos:notifications.setting')->findOrCreate(array(
            'person' => $viewer,
            'actor'	 => $data->actor
        ));
       
        $setting->setValue('posts', null, $data->email);
        
        $setting->save();
    }
    
    /**
     * Authorizes a post, only if the viewer is already following the owner
     *
     * @return boolean
     */
    public function canPost(KConfig $data)
    {         
         $viewer  = get_viewer();
         
         if ( !$data->actor )
             return false;
         
         if ( $viewer->eql($data->actor) )
             return false;
             
         if ( !$data->actor->isFollowable() )
             return false;
         
         if ( !$data->actor->isSubscribable() )
             return false;
         
         return $viewer->following( $data->actor );         
    }
}