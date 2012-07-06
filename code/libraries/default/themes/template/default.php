<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Themes
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Default Template
 *
 * @category   Anahita
 * @package    Lib_Themes
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibThemesTemplateDefault extends LibBaseTemplateAbstract
{
	/**
	 * Content Buffer
	 * 
	 * @var array
	 */
	protected $_buffer;
	
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
		
		$this->_buffer  	=  new KConfig($config->buffer);

		$buffer	  = pick(KConfig::unbox($this->_buffer->component), array());
		$this->_buffer->component  = implode('', $buffer);
		
		$this->_document	=  $config->document;
		
		foreach( $config->paths as $path ) {
			$this->addPath($path);
		}
		
		$this->addFilter( $config->filters );
        
        //load theme language
        JFactory::getLanguage()->load( 'tpl_'.$this->getIdentifier()->package);		
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
        $identifier = clone $this->getIdentifier();
        $identifier->path = array();        
        
		$config->append(array(
			'paths'		=> array(dirname(__FILE__).'/layouts', dirname($identifier->filepath)),			
		    'filters'   => array('shorttag'),
            'document'  => JFactory::getDocument(),
            'data'      => array(
                'debug' => JDEBUG
             )
		));
        
        if ( $config->document )
        {
           $config->append(array(
                'buffer' => isset($config->document->_buffer) ? $config->document->_buffer : array()
           ));
        }
        
        $content = '';
        
        if (is_readable( dirname($identifier->filepath).DS.'params.ini' ) ) {
            $content = file_get_contents(dirname($identifier->filepath).DS.'params.ini');
        }
        
        $config->append(array(
            'data' => array('params' => new JParameter($content))
        ));
                 			
		if ( KRequest::get('get.tmpl', 'cmd') != 'raw' ) {
			$config->append(array(
				'filters'	=> array('header', 'module','component','message')
			));
		}
				
		parent::_initialize($config);
	}
    
	/**
	 * Returns the Template Document
	 * 
	 * @return JDocument
	 */
	public function getDocument()
	{
		return $this->_document;
	}
	
	/**
	 * Returns the buffer
	 * 
	 * @return KConfig
	 */
	public function getBuffer()
	{
		return $this->_buffer;
	}
}