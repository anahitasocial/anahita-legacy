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
 * Commandable Behavior
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerBehaviorCommandable extends KControllerBehaviorAbstract
{
	/**
	 * Renders the navigation toolbar 
	 *
	 * @param KCommandContext $context
	 */
	protected function _afterControllerGet(KCommandContext $context)
	{
	    $can_render = is_string($context->result)  && 
	                  $this->isDispatched()        &&
	                  KRequest::type()   == 'HTTP' &&
	                  KRequest::format() == 'html';

		if ( $can_render)
		{
		    $ui     = $this->getView()->getTemplate()->getHelper('ui');
		    
		    $data = array(	
		       'menubar'  => $this->getView()->menubar,
		       'actorbar' => $this->getView()->actorbar,
		       'toolbar'  => $this->getView()->toolbar    
            );
		    
		    $context->result = $ui->header($data).$context->result;		    
		}
	}
}