<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Abstract Medium Controller
 *
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class ComMediumControllerAbstract extends ComBaseControllerService
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
		
		$this->registerCallback(array('after.add'), array($this, 'createStory'));
		
		$this->_request->append(array(
			'filter'=>'',
			'grid'=>0
		));
        
        //add the anahita:event.command        
        $this->getCommandChain()
            ->enqueue( $this->getService('anahita:command.event'), KCommand::PRIORITY_LOWEST);        
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
	        'behaviors'         => array('composable','publisher')
	    ));
	
	    parent::_initialize($config);
	}
		
	/** 
	 * Browse Action
	 * 
	 * @param KCommandContext $context Context Parameter
	 * 					
	 * @return AnDomainQuery
	 */
	protected function _actionBrowse($context)
	{		
		$context->append(array(
			'viewer' => get_viewer()
		));
		
		$entities = parent::_actionBrowse($context);
		
		$data	  = $context->data;
		
		if ( $this->getRepository()->hasBehavior('ownable') )
			$data->append(array(
				'actor' => get_viewer()
			));
			
        if( $this->filter == 'leaders' )
        {
           $leaderIds = array();
           $leaderIds[] = $context->viewer->id;
           $leaderIds[] = $context->viewer->getLeaderIds()->toArray();
           $entities->where( 'owner.id','IN', $leaderIds );
        }
		elseif( $this->getRepository()->hasBehavior('ownable')  )
			$entities->where('owner', '=', $data->actor);
			
		return $entities;
	}
		
	/** 
	 * Delete Action
	 * 
	 * @param KCommandContext $context Context Parameter
	 * 
	 * @return void
	 */
	protected function _actionDelete(KCommandContext $context)
	{
		$data         = $context->data;
		
		$redirect_url = array('view'=>KInflector::pluralize($this->getIdentifier()->name), 'oid'=>$data->entity->owner->id);
		
		parent::_actionDelete($context);
				
		$this->setRedirect($redirect_url);
	}
	
	/**
	 * Set the default Actor View
	 *
	 * @param KCommandContext $context Context parameter
	 *
	 * @return ComActorsControllerDefault
	 */
	public function setView($view)
	{
	    parent::setView($view);
	
	    if ( !$this->_view instanceof ComBaseViewAbstract )
	    {
	        $name  = KInflector::isPlural($this->view) ? 'media' : 'medium';
	        $defaults[] = 'ComMediumView'.ucfirst($view).ucfirst($this->_view->name);
            $defaults[] = 'ComMediumView'.ucfirst($name).ucfirst($this->_view->name);
            $defaults[] = 'ComBaseView'.ucfirst($this->_view->name);
            
	        register_default(array('identifier'=>$this->_view, 'default'=>$defaults)); 
	    }
	
	    return $this;
	}	
}