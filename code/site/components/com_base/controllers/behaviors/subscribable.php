<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Subscribable Behavior
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerBehaviorSubscribable extends KControllerBehaviorAbstract 
{
    /**
     * If the viewer is subscribe then unsubscribe, if not subscribe then subscribe
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */    
    protected function _actionTogglesubscription($context)
    {
        $entity = $context->data->entity;
        
        if ( $entity->subscribed(get_viewer()) )
        {  
            $ret = $this->_mixer->execute('unsubscribe', $context);       
        } 
        else 
        {
            $ret= $this->_mixer->execute('subscribe', $context);
        }
        
        return $ret;
    }
    
	/**
	 * Subscribe the viewer to the subscribable object
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _actionSubscribe($context)
	{
		$entity = $context->data->entity;
		$entity->addSubscriber(get_viewer());
	}
	
	/**
	 * Remove the viewer's subscription from the subscribable object
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _actionUnsubscribe($context)
	{
		$entity = $context->data->entity;
		$entity->removeSubscriber(get_viewer());
	}
}

?>