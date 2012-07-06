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
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Comment Controller
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerComment extends ComBaseControllerService
{
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */	
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->registerCallback(array('after.voteup','after.votedown'), array($this,'getvoters'));
        
        $this->getCommandChain()
            ->enqueue( $this->getService('anahita:command.event'), KCommand::PRIORITY_LOWEST);        
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
		    'behaviors' => array('publisher'),
			'publish_comment'  => KRequest::format() == 'html'			
		));
	
		parent::_initialize($config);
	}
		
	/**
	 * Creates a comment
	 * 
	 * @param  KCommandContext $context
	 * @return AnDomainEntityAbstract
	 */
	protected function _actionAdd($context)
	{
		$data = $context->data;
		$body = $data->body;
		$data->comment = $data->entity = $data->parent->addComment($body);
		return $data->entity;
	}
	
	/**
	 * Delete a Comment belonging to a node
	 * 
	 * @param KCommandContext $context post data
	 * @return boolean
	 */
	protected function _actionDelete($context)
	{
		$data = $context->data;
		$data->entity->delete();
	}
	
	/**
	 * Edit a comment
	 * 
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _actionEdit($context)
	{
		$data = $context->data;		
		$data->comment->body = $data->body;
		return $data->entity;
	}	
	
	/**
	 * Sets the default view to the comment views
	 * 
	 * @param 	stirng $view
	 * @return	ComCommentsControllerResource
	 */
	public function setView($view)
	{
		parent::setView($view);

		if ( !$this->_view instanceof LibBaseViewAbstract ) 
		{
			$view       = KInflector::isPlural($view) ? 'comments' : 'comment';
            $defaults[] = 'ComBaseView'.ucfirst($view).ucfirst($this->_view->name);
            $defaults[] = 'ComBaseView'.ucfirst($this->_view->name);
			register_default(array('identifier'=>$this->_view, 'default'=>$defaults));
		}
		
		return $this;
	}	
	
	/**
	 * Generic executation authorization
	 * 
     * @param KCommandContext $context The CommandChain Context
     *
     * @return boolean
	 */
	public function canExecute(KCommandContext $context)
	{
        $data    = $context->data;
		$parent  = $data->parent;				
		$comment = $data->entity;		
		//either one of them has to exists
		if ( !pick($parent, $comment) )
			return false;
			
		$data->parent = $parent = $parent ? $parent : $comment->parent;
		
		if ( !$parent->allows(get_viewer() , 'access')) {
			return false;
		}
        
        return $this->__call('canExecute', array($context));
	}
    
	/**
	 * Returns whether a comment can be added
	 * 
	 * @param KConfig $data Data
	 *
	 * @return boolean
	 */
	public function canAdd(KConfig $data)
	{
	    $parent = $data->parent;
	    
	    return $parent->authorize('add.comment');
	}

	/**
	 * Returns whether a comment can be added
	 *
	 * @param KConfig $data Data
	 *
	 * @return boolean
	 */
	public function canEdit(KConfig $data)
	{
	    $comment = $data->comment;
	   
	    return $comment->authorize('edit');
	}
		
	/**
	 * Returns whether a comment can be added
	 * 
	 * @param KConfig $data Data
	 *
	 * @return boolean
	 */
	public function canDelete(KConfig $data)
	{
	    $comment = $data->comment;
	    
	    return $comment->authorize('delete');
	}
}