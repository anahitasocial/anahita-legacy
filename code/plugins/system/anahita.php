<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Plugins
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

jimport('joomla.plugin.plugin');

require_once JPATH_PLUGINS.'/system/koowa.php';

/**
 * Anahita System Plugin
 * 
 * @category   Anahita
 * @package    Plugins
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class PlgSystemAnahita extends PlgSystemKoowa 
{
	/**
	 * Constructor
	 * 
	 * @param mixed $subject Dispatcher
	 * @param array $config  Array of configuration
     * 
     * @return void
	 */
	public function __construct($subject, $config = array())
	{	    
		parent::__construct($subject,$config);
		
		JLoader::import('anahita.anahita', JPATH_LIBRARIES);
		
        $cache_enabled = JFactory::getApplication()->getCfg('caching');
        
		//instantiate anahita
        Anahita::getInstance(array(
            'koowa'         => Koowa::getInstance(), 
			'cache_prefix'  => md5(JFactory::getApplication()->getCfg('secret')).'-cache-koowa',
			'cache_enabled' => $cache_enabled
        ));

        if ( !$cache_enabled ) {
            //clear apc cache for module and components
            //@NOTE If apc is shared across multiple services
            //this causes the caceh to be cleared for all of them
            //since all of them starts with the same prefix. Needs to be fix
            clean_apc_with_prefix('cache_mod');
            clean_apc_with_prefix('cache_com');
            clean_apc_with_prefix('cache__system');
        }
        
		KService::get('plg:storage.default');
		
        if ( JDEBUG && JFactory::getApplication()->isAdmin() )
        {
            JError::raiseNotice('','Anahita is running in the debug mode. Please make sure to turn the debug off for production.');
        }
        
        JFactory::getLanguage()->load('overwrite',   JPATH_ROOT);
		JFactory::getLanguage()->load('lib_anahita', JPATH_ROOT);
	}
		
	/**
	 * onAfterInitialise handler
	 *
	 * Adds the mtupgrade folder to the list of directories to search for JHTML helpers.
	 * 
	 * @return null
	 */
	public function onAfterRoute()
	{
		$type 	= strtolower(KRequest::get('request.format', 'cmd', 'html'));
				
		$format = $type;
		
		if ( KRequest::type() == 'AJAX' ) {
		    $format = strtolower(KRequest::get('server.HTTP_X_REQUEST', 'cmd', 'raw'));
		}
		
		$document =& JFactory::getDocument();
		
		//if a document type is raw then convert it to HTML
		//and set the format to html
		if ( $format == 'raw' )
		{
		    $document = JDocument::getInstance('html');
		
		    //set the format to html
		    $format = 'html';
		    //set the tmpl to raw
		    JRequest::setVar('tmpl', 		'raw');
		}
		
		KRequest::set('get.format',		$format);
				
		//wrap a HTML document around a decorator
		if (JFactory::getDocument()->getType() == 'html')
		{
		    if ( $format == 'html' )
		    {
		        //set the document
		        $document = JFactory::getApplication()->isAdmin() ? 
		            JDocument::getInstance('html') : 
		            new AnDocumentDecorator($document); 
		    }		        
		    else {
		        $document = JDocument::getInstance('raw');
		    }
		}

        if ( !JFactory::getApplication()->isAdmin() )
        {
            //set the error document
            $error =& JDocument::getInstance('error');
            $error = new AnDocumentDecorator($error);
        }
        
		$tag   = JFactory::getLanguage()->getTag();
		
		if ( JFactory::getApplication()->isAdmin() )
		{
		    JHTML::script('lib_koowa/js/koowa.js', 	   'media/');
		    JHTML::script('lib_anahita/js/anahita.js?lang='.$tag.'&token='.JUtility::getToken(), 'media/');
		    JHTML::script('lib_anahita/js/admin.js',   'media/');
		}
		else
		{
		    JHTML::script('lib_anahita/js/min/bootstrap.js', 'media/');
		    JHTML::script('lib_anahita/js/anahita.js?lang='.$tag.'&token='.JUtility::getToken().'&'.uniqid(), 'media/');
		    JHTML::script('lib_anahita/js/site.js?'.uniqid(), 'media/');
		}
		
		if ( !JFactory::getApplication()->isAdmin() ) 
		{
    		KService::get('com://site/default.filter.string');
		}	    	
	}
		
	/**
	 * onAfterRender handler
	 * 
	 * @return void
	 */
	public function onAfterDispatch()
	{
		
	}

	/**
	 * Renders the response progressively
	 *
	 * @return void
	 */
	public function onAfterRender()
	{
       //JResponse::setHeader('Transfer-Encoding','chunked');
       //$chunks = KService::get('com://site/base.template.helper.string')->chunkify(JResponse::getBody());
       //JResponse::setBody(implode('', $chunks));
	}
	
	/**
	 * store user method
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param 	array		holds the new user data
	 * @param 	boolean		true if a new user is stored
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	public function onAfterStoreUser($user, $isnew, $succes, $msg)
	{
		global $mainframe;

		if( !$succes )
			return false;
        
        $person =   KService::get('repos://site/people.person')
                    ->getQuery()
                    ->disableChain()
                    ->userId($user['id'])
                    ->fetch();
                    ;
							
		if ( $person ) 
		{		    
			KService::get('com://site/people.helper.person')->synchronizeWithUser($person, JFactory::getUser($user['id']) );
			
		} else 
		{
			$person = KService::get('com://site/people.helper.person')->createFromUser( JFactory::getUser($user['id']) );
		}
		
		$person->save();
		
		return true;
	}
	

	/**
	 * store user method
	 *
	 * Method is called before user data is deleted from the database
	 *
	 * @param 	array		holds the user data
	 */
	public function onBeforeDeleteUser($user)
	{							
		$person = 	KService::get('repos://site/people.person')
                    ->getQuery()
                    ->disableChain()
                    ->userId($user['id'])
					->fetch();
					;
		
		if(!$person)
			return;

		$apps = KService::get('repos://site/apps.app')->getQuery()->disableChain()->fetchSet();
		
		foreach($apps as $app) 
		{
		    KService::get('anahita:event.dispatcher')->addEventSubscriber($app->getDelegate());
		}
		
		$person->destroy();
	}
}

/**
 * Document Decorator. Decorates the render method and uses the TmplAbstract class 
 * to render the template
 * 
 * @category   Anahita
 * @package    Plugins
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDocumentDecorator
{
    /**
     * Document
     * 
     * @var JDocumentHTML
     */
    protected $_document;
    
    /**
     * Document Object
     *
     * @param JDocumentHTML $document Document object
     * 
     * @return void
     */
    public function __construct($document)
    {
        $this->_document = $document;
    }
    
    /**
     * Overrwrites the JDocumentHtml::render()
     *
     * @access public
     * @param boolean 	$cache		If true, cache the output
     * @param array		$params		Associative array of attributes
     * @return 	The rendered data
     */
    function render( $caching = false, $params = array())
    {   
        $params     = new KConfig($params);
        
        $params->append(array(
            'file' => $this->_document instanceof JDocumentError ? 'error.php' : 'index.php'
        )); 
               
        $tmpl  = JFile::stripExt(JFile::getName($params['file']));        
               
        $data['filename'] = $params['file'];
        
        $document         = $this->_document;
        
        if ( $this->_document instanceof JDocumentError )
        {
            JResponse::setHeader('status', $this->_document->_error->code.' '.str_replace( "\n", ' ', $this->_document->_error->message ));
            
            $error = $this->_document->_error;
            
            if ( !isset($error) ) {
                $error = JError::raiseWarning( 403, JText::_('ALERTNOTAUTH') );    
            }
            
            $document      = JFactory::getDocument();
            
            $data['error']     = $error;
            $data['backtrace'] = $this->_document->renderBacktrace(); 
        }
        
        $identifier = 'tmpl://site/'.$params['template'].'.template';
        
        $template   = KService::get($identifier,array(                
                        'document'      => $document,
                        'data'          => $data        
                      ));
                      
        return $template->loadTemplate($tmpl)->render();        
    }
    
    /**
     * Forwards __get to the docuemnt
     *
     * @param string $key
     * 
     * @return mixed
     */
    public function __get($key)
    {
        return $this->_document->$key;
    }
    
    /**
     * Forwards the call to the JDocuemntHTML object
     *
     * @param string $method    The called method
     * @param array  $arguments Array of arguments
     * 
     * @return mixed Return the JDocumentHtml::$method($arguments)
     */
    public function __call($method, $arguments)
    {
        return call_object_method($this->_document, $method, $arguments);
    }
}