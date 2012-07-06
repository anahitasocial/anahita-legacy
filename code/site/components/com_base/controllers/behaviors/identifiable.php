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
 * Identifiable Behavior
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerBehaviorIdentifiable extends LibBaseControllerBehaviorIdentifiable
{	    	
    /**
     * Fetches an entity
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return AnDomainEntityAbstract The identified entity
     */
    public function fetchEntity(KCommandContext $context)
    {    	
    	$data   = $context->data;
    	
    	if ( $this->getRepository()->isOwnable() && $data->actor ) {
    		$context->identity_scope = array('owner'=>$data->actor); 
    	}

    	$entity = parent::fetchEntity($context);
    	
    	//if entity is ownable add the owner as actor if not already set
    	if ( $entity && $entity->isOwnable() ) 
    	{
    		$data->append(array(
    			'actor' => $entity->owner
    		));
    	}
    	
    	return $entity;
    }	
}