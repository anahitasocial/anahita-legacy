<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Service Controller
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerService extends ComBaseControllerResource
{
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
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
		parent::_initialize($config);
		
		$this->_entity = $config->entity;
		
		$behaviors = array('identifiable','commandable');
		
		if ( $this->getRepository()->hasBehavior('ownable') ) {
			$behaviors[] = 'ownable';
		}
				
		if ( $this->getRepository()->hasBehavior('commentable') ) {
			$behaviors[] = 'commentable';
		}

		
		if ( $this->getRepository()->hasBehavior('privatable') ) {
			$behaviors[] = 'privatable';
		}
		
		if ( $this->getRepository()->hasBehavior('parentable') ) {
			$behaviors[] = 'parentable';
		}		

		if ( $this->getRepository()->hasBehavior('subscribable') ) {
		    $behaviors[] = 'subscribable';
		}
				
		if ( KRequest::method() == 'POST' )
		{
			if ( $this->getRepository()->hasBehavior('enableable') ) {
				$behaviors[] = 'enablable';
			}
		}
						
		if ( $this->getRepository()->hasBehavior('votable') ) {
			$behaviors[] = 'votable';
		}		
				
		$config->append(array(
			'behaviors'=>$behaviors
		));		
	}

	/**
	 * Generic POST action for a medium. If an entity exists then execute edit
	 * else execute add
	 * 
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _actionPost($context)
	{
		$data 	= $context->data;
							
		$data->append(array(
			'_action' => !$data->entity ? 'add' : 'edit'
		));

		$result = $this->execute($data->_action, $context);
		
		if ( is($result, 'AnDomainEntityAbstract') && $result->isDescribable() ) {
			$this->setRedirect($result->getURL());
		}
		
		return $result;
	}
	
	/**
	 * Add Action
	 * 
	 * @param  KCommandContext $context
	 * @return AnDomainEntityAbstract
	 */
	protected function _actionAdd($context)
	{
		$data   = $context->data;
		$entity = $this->getRepository()->getEntity()->setData($data);
		$data->entity = $data->{$this->getIdentifier()->name} = $entity;		
		return $data->entity;
	}
	
	/**
	 * Edit Action
	 * 
	 * @param  KCommandContext $context
	 * @return AnDomainEntityAbstract
	 */
	protected function _actionEdit($context)
	{
		$data			 = $context->data;
		$entity			 = $data->entity;
		$entity->setData($data);					
		return $entity;
	}
	
	/**
	 * Delete Action
	 *
	 * @param  KCommandContext $context
	 * @return AnDomainEntityAbstract
	 */
	protected function _actionDelete($context)
	{
		$data = $context->data;
		$data->entity->delete();
		$this->setRedirect('index.php?option=com_'.$this->getIdentifier()->package.'&view='.KInflector::pluralize($this->getIdentifier()->name));
	}
		
}