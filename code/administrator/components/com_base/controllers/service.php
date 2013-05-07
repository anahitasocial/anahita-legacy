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
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Resource Controller
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
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->registerActionAlias('apply', 'post');
		$this->registerActionAlias('save',  'post');
	}
		
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
		    'toolbars'    => array('menubar', $this->getIdentifier()->name),		        
			'behaviors' => to_hash(array(
				'serviceable', 'persistable'
			)),
			'request' => array(
				'limit' 	=> 20,
                'sort'      => 'id',
                'direction' => 'ASC'                
			)
		));
				
		parent::_initialize($config);
	}

	/**
	 * Return the item
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return mixed
	 */
	protected function _actionRead(KCommandContext $context)
	{
	    //create an empty entity for the form layout;
	    if ( !$this->getItem() ) {
	        $this->setItem($this->getRepository()->getEntity()->reset());
	    }
	    return $this->getItem();
	}
	
	/**
	 * Saves/Add an entity and then redirects
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return AnDomainEntitysetAbstract
	 */
	protected function _actionPost($context)
	{
		if ( $context->action == 'save' )
			$context->response->setRedirect('option=com_'.$this->getIdentifier()->package.'&view='.KInflector::pluralize($this->getIdentifier()->name));
		
		$data = $context->data;

		//searches for any \w+_id pattern and then set a relationship
		//accordingly
		//Should be moved to a behavior 
		foreach($data as $key => $value) 
		{
			if ( strpos($key,'_id') ) 
			{
				$key = str_replace('_id', '', $key);
				$this->getState()->$key = $this->getRepository($key)->fetch($value);
			}
		}
		
		if ( $this->getItem() ) 
			$this->execute('edit', $context);
		else {
            $this->execute('add',  $context);
        }
						
		return $this->getItem();
	}
	
	/**
	 * Cancel action
	 * 
	 * This function will unlock the row(s) and set the redirect to the referrer
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRow	A row object containing the data of the cancelled object
	 */
	protected function _actionCancel(KCommandContext $context)
	{
		//Create the redirect		
		$context->response->setRedirect(JRoute::_('option=com_'.$this->getIdentifier()->package.'&view='.KInflector::pluralize($this->getIdentifier()->name)));	
	}
	
	/**
	 * Create a new entity
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return AnDomainEntityAbstract
	 */
	protected function _actionAdd($context)
	{
		$data 	= $context->data;
		
		$entity = $this->getRepository()->getEntity();
		
		$entity->setData($data);
		
		if ( $entity->isDictionariable() && $data->meta ) 
		{
		    foreach($data->meta as $key => $value) 
		    {
		        $entity->setValue($key, $value);
		    }
		}
				
		return $this->setItem($entity)->getItem();
	}
	
	/**
	 * Edits an entity
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return AnDomainEntityAbstract
	 */
	protected function _actionEdit($context)
	{
	    $entity = parent::_actionEdit($context);
	    
		if ( $entity->isDictionariable() && $data->meta ) 
		{
		    foreach($data->meta as $key => $value) {
		        $entity->setValue($key, $value);
		    }
		}
		
		return $entity;
	}		
	
	/**
	 * Return a set of entities
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return AnDomainEntitysetAbstract
	 */	
	protected function _actionBrowse($context)
	{
		$data  = $context->data;
        
		$context->append(array(
			'query' => $this->getRepository()->getQuery()
		));
		
		$query = $context->query;
		
		$query->order($this->sort, $this->direction)
			  ->limit($this->limit, $this->limitstart);
		
		$query->keyword = $this->search;
		
		if ( $this->getRepository()->hasBehavior('parentable') ) {
			if ( $this->getState()->parent ) {
				$query->parent($this->getState()->parent);				
			} elseif ($this->pid == -1) {
				$query->parent(null);				
			}
		}
		
		return $this->getState()->setList($query->toEntitySet())->getList();
	}
}
