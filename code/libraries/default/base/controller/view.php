<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * View Controller. This conroller doesn't require domain entities
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseControllerView extends KControllerAbstract
{
    /**
     * View object or identifier (APP::com.COMPONENT.view.NAME.FORMAT)
     *
     * @var string|object
     */
    protected $_view;
                
    /**
     * Redirect options
     *
     * @var KConfig
     */
    protected $_redirect;
    
    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options.
     */
    public function __construct( KConfig $config)
    {    
        parent::__construct($config);    
        
        $this->_redirect = new KConfig();
        
        $this->_view     = $config->view;
        
        //register display as get so $this->display() return
        //$this->get()
        $this->registerActionAlias('display', 'get');
        
        // Mixin the toolbar
        if($config->dispatch_events) {
            $this->mixin(new KMixinToolbar($config->append(array('mixer' => $this))));
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
            'behaviors' => array('sanitizable'),
            'request' 	=> array('format' => 'html'),
        ))->append(array(
            'view' 		=> $config->request->get ? $config->request->get : ($config->request->view ? $config->request->view : $this->getIdentifier()->name)
        ));
        
        parent::_initialize($config);
    }

    /**
     * Empty Browse Action
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    protected function _actionBrowse(KCommandContext $context) 
    {
        //Implemented by the subclasses    
    }

    /**
     * Empty Read Action
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    protected function _actionRead(KCommandContext $context) 
    {
        //Implemented by the subclasses    
    }    
    
    /**
     * Browse Action
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return string
     */    
    protected function _actionGet(KCommandContext $context)
    {
        $action = null;
        
        if ( $this->get ) {
            $action = strtolower('get'.$this->get);
            if ( !in_array($action, $this->getActions()) ) {
                $action = null;
            }
        }
        
        if ( !$action )
            $action = KInflector::isPlural($this->view) ? 'browse' : 'read';
        
        $result = $this->execute($action, $context);
        
        if ( $result !== false && !is_string($result) )
            $result = $this->render($context);
            
        return (string) $result;
    }
    
    /**
     * Post action
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */    
    protected function _actionPost(KCommandContext $context)
    {
        $action = $context->data->_action;
        
        if ( in_array($action, $this->getActions()) ) {
            $this->execute($action, $context);
        }
        
        return $context->result;
    }
        
    /**
     * Get the view object attached to the controller
     * 
     * @return LibBaseViewAbstract
     */
    public function getView()
    {
        if(!$this->_view instanceof LibBaseViewAbstract)
        {
            //Make sure we have a view identifier
            if(!($this->_view instanceof KServiceIdentifier)) {
                $this->setView($this->_view);
            }
            
            $config = array(
              'dispatched' => $this->_dispatched
            );
            
            $this->_view = $this->getService($this->_view, $config);
        }
        
        return $this->_view;
    }
    
    /**
     * Method to set a view object attached to the controller
     *
     * @param mixed $view An object that implements KObjectIdentifiable, an object that 
     * implements KIndentifierInterface or valid identifier string
     *                  
     * @throws KDatabaseRowsetException If the identifier is not a view identifier
     * 
     * @return KControllerAbstract
     */
    public function setView($view)
    {
        if(!($view instanceof ComBaseViewAbstract))
        {
            if(is_string($view) && strpos($view, '.') === false ) 
            {
                $identifier          = clone $this->getIdentifier();
                $identifier->path    = array('view', $view);
                $identifier->name    = $this->format ? $this->format : 'html';
            }
            else $identifier = $this->getIdentifier($view);
            
            register_default(array('identifier'=>$identifier, 'prefix'=>$this, 'name'=>'View'.ucfirst($identifier->name)));
            
            $view = $identifier;
        }
        
        $this->_view = $view;
                
        return $this;
    }
            
    /**
     * Set a URL for browser redirection.
     *
     * @param array $url     The url to set the controller redirect to
     * @param array $options Options to set the message and type for the redirect
     * 
     * @return void
     */
    public function setRedirect($url = 'back', $options = array())
    {
        if ( !is_array($options) )
            $options = array('message'=>$options);
            
        $options['url'] = (string)$url;
        
        $options         = new KConfig($options);
        
        $options->append(array(
            'message'    => '',
            'type'        => null
        ));
        
        if ( $options->url == 'back') {
            $options->url = (string)KRequest::referrer();
        }
            
        $options->url = LibBaseHelperUrl::getRoute($url);

        $this->_redirect = $options;
            
        if ( KRequest::method() == 'GET' )
        {
            JFactory::getApplication()->redirect($options->url, $options->message, $options->type);
        }
        
        return $this;
    }
    
    /**
     * Return a repository based on the $entity argument.
     *
     * @param string $identifier The identifier of a repository
     *
     * @return AnDomainRepositoryAbstract
     */
    public function getRepository( $identifier )
    {
        if ( is_string($identifier) && strpos($identifier, '.') === false )
        {
            $name       = $identifier;
            $identifier = clone $this->getIdentifier();
            $identifier->path = array('domain','entity');
            $identifier->name = $name;
        }
    
        return KService::get($identifier)->getRepository();
    }    
            
    /**
     * Returns an array with the redirect url, the message and the message type
     *
     * @return KConfig Named array containing url, message and messageType, or null 
     * if no redirect was set
     */
    public function getRedirect()
    {
        return $this->_redirect;
    }
       
	/**
	 * Get a behavior by identifier
	 *
	 * @param mixed $behavior Behavior name
	 * @param array $config   An array of options to configure the behavior with
	 *
	 * @see KMixinBehavior::getBehavior()
	 *
	 * @return AnDomainBehaviorAbstract
	 */
	public function getBehavior($behavior, $config = array())
	{
	    if ( is_string($behavior) )
	    {
	        if ( strpos($behavior,'.') === false )
	        {
		        $identifier       = clone $this->getIdentifier();
		        $identifier->path = array('controller','behavior');			        			        
		        $identifier->name = $behavior;
		        register_default(array('identifier'=>$identifier, 'prefix'=>$this));			    
			    $behavior = $identifier;
	        }
	    }
	   
	    return parent::__call('getBehavior', array($behavior, $config));
	}

	/**
	 * Renders the controller's view by passing the $data to the view
	 *
	 * @param KCommandContext $context Context 
	 *
	 * @return string
	 */
	public function render(KCommandContext $context)
	{
        $data = $context->data;
        $data = KConfig::unbox($data);
        
        if ( $this->getView() instanceof LibBaseViewHtml ) {
	       //merge data with request and pass them to the vie
	       $data = array_merge(KConfig::unbox($this->getRequest()), $data);
        }
	
	    return (string) $this->getView()->set($data)->display();
	}
		
	/**
	 * Get a toolbar by identifier
	 *
	 * @return KControllerToolbarAbstract
	 */
	public function getToolbar($toolbar, $config = array())
	{
	    if ( is_string($toolbar) )
	    {
	        if ( strpos($toolbar,'.') === false )
	        {
	            $identifier       = clone $this->getIdentifier();
	            $identifier->path = array('controller','toolbar');
	            $identifier->name = $toolbar;
	            register_default(array('identifier'=>$identifier, 'prefix'=>$this));
	            $toolbar = $identifier;
	        }
	    }	    
	    
	    return parent::__call('getToolbar', array($toolbar, $config));	    
	}
	
	/**
	 * Supports a simple form Fluent Interfaces. Allows you to set the request
	 * properties by using the request property name as the method name.
	 *
	 * For example : $controller->view('name')->limit(10)->browse();
	 *
	 * @param	string	Method name
	 * @param	array	Array containing all the arguments for the original call
	 * @return	KControllerBread
	 *
	 * @see http://martinfowler.com/bliki/FluentInterface.html
	 */
	public function __call($method, $args)
	{
	    //Handle action alias method
	    if(!in_array($method, $this->getActions()) && count($args) )
	    {
	        //Check first if we are calling a mixed in method.
	        //This prevents the model being loaded durig object instantiation.
	        if(!isset($this->_mixed_methods[$method]))
	        {
	            $this->$method = $args[0];
	        
	            if($method == 'view') {
	                $this->_view = $args[0];
	            }
	        
	            return $this;
	        }
	    }
	    
	    return parent::__call($method, $args);
	}
	
	/**
     * Executes a GET request and display the view
     * 
     * @return string
	 */
	public function __toString()
	{
        try {
	       return $this->display();
        } catch(Exception $e) {
            trigger_error('Exception in '.get_class($this).' : '.$e->getMessage(), E_USER_WARNING);
            throw $e;
        }
	}
}