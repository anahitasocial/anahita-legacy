<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Default Base Dispatcher
 *
 * @category   Anahita
 * @package    Com_Base
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseDispatcherDefault extends LibBaseDispatcherComponent
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
		
		if ( $config->auto_asset_import  ) {
			$this->registerCallback('after.get', array($this, 'importAsset'));
		}
		
		if ( $config->request->getFormat() == 'html' &&
		     !$config->request->isAjax()
		        ) {
		    set_exception_handler(array($this, 'exception'));
		}
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
	        'auto_asset_import' => true
	    ));
	    
	    $config->auto_asset_import = $config->auto_asset_import && (KRequest::method() == 'GET' && KRequest::type() == 'HTTP');
	
	    parent::_initialize($config);
        
        if ( $config->request->view ) {
            $config->controller = $config->request->view;    
        }
	}	  
	
  	/**
  	 * Import component assets automatically 
  	 * 
  	 * This method automatically imports the js/css assets of the app
  	 * 
     * @return mixed
     */
	public function importAsset()
	{
	    $asset = $this->getService('com://site/base.template.asset');
	    
		$url = $asset->getURL("com_{$this->getIdentifier()->package}/js/{$this->getIdentifier()->package}.js");
		
		if ( $url )
			JFactory::getDocument()->addScript($url);
			
		$url = $asset->getURL("com_{$this->getIdentifier()->package}/css/{$this->getIdentifier()->package}.css");
								
		if ( $url )
			JFactory::getDocument()->addStyleSheet($url);
	}
    
    /**
     * Renders a controller view
     * 
     * @return string
     */
    protected function _actionRender(KCommandContext $context)
    {                                       
        if ( $context->request->getFormat() == 'html' && KRequest::type() == 'HTTP' ) {
            $this->_setPageTitle();
        }
               
        return parent::_actionRender($context);   
    }
    
    /**
     * Sets the page title/description
     * 
     * @return void
     */
    protected function _setPageTitle()
    {
        $view     = $this->getController()->getView();
        $document = JFactory::getDocument();
        
        $item     = $this->getController()->getState()->getItem();
        $actorbar = $this->getController()->actorbar;
        
        $title = array();
        $description = null;                               
        
        if ( $actorbar && $actorbar->getActor() ) 
        {
            if ( $actorbar->getTitle() )
                $title[] = $actorbar->getTitle();
                
            $description = $actorbar->getDescription();      
        }
        else {
            $title[] = ucfirst($view->getName());   
        }
        
        if ( $item && $item->isDescribable() ) {
            array_unshift($title, $item->name);
            $description = $item->body;
        }
     
        $title = implode(' - ', array_unique($title));      
        $document->setTitle($title);
        $description = preg_replace( '/\s+/', ' ', $description );
        $description = htmlspecialchars($view->getTemplate()->renderHelper('text.truncate', $description, array('length'=>160)));          
        $document->setDescription($description);         
    }
    
    /**
     * Allows the component to handle exception. By default this
     * action passes the exception to the application exception handler
     * 
     * @param KCommandContext $context Command context
     * 
     * @return void
     */
    protected function _actionException(KCommandContext $context)
    {       
        if ( !JFactory::getUser()->id && 
                $context->data instanceof LibBaseControllerExceptionUnauthorized ) 
        {
            $this->getController()->setMessage('COM-PEOPLE-PLEASE-LOGIN-TO-SEE');
            $return = base64_encode(KRequest::url());
            $context->response->setRedirect(JRoute::_('option=com_people&view=session&return='.$return));
            $context->response->send();
            exit(0);
        }
        else { 
            $this->getService('application.dispatcher')
                ->execute('exception', $context);      
        }
    }
}