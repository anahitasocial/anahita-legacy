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
 * Restful Controller
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerResource extends LibBaseControllerResource
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

        //load the language
        JFactory::getLanguage()->load( $config->language );
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
		    'language'      => 'com_'.$this->getIdentifier()->package ,
		    'toolbars'      => array($this->getIdentifier()->name,'menubar','actorbar'),		        
		    'behaviors'		=> array('loggable','executable','validatable'),
			'request'		=> array(
				'limit' 	=> 20,
				'offset'	=> 0				
			)
		));
				
		parent::_initialize($config);
		
	}
    		
	/** 
	 * Service Browse
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return AnDomainQuery
	 */	
	protected function _actionBrowse($context)
	{
		$data  = $context->data;
		
		$context->append(array(
			'query' => $this->getRepository()->getQuery() 
		));
		
		$query = $context->query;
		
		if ( $this->q ) {
			$data->search_keyword = $query->keyword = explode(' OR ', $this->q);			
		}
		
		if ( $this->hasBehavior('parentable') && $data->parent ) {
			$query->parent($data->parent);
		}
		
		//do some sorting
		if ( $this->sort ) {
			$dir = $this->_request->get('direction','asc');
			$query->order($this->sort, $dir);
		}
		
		$query->limit( $this->limit , $this->start );
		$data->{KInflector::pluralize($this->getIdentifier()->name)} =
		$data->entities = $query->toEntitySet();
		return $data->entities;
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
	        //if actorbar or menu alawys default to the base
	        if ( in_array($toolbar, array('actorbar','menubar','comment')) )
	        {
	            $identifier       = clone $this->getIdentifier();
	            $identifier->path = array('controller','toolbar');
	            $identifier->name = $toolbar;	            
                register_default(array('identifier'=>$identifier, 'default'=>'ComBaseControllerToolbar'.ucfirst($toolbar)));                
	            $toolbar = $identifier;
	        }
	    }
	
	    return parent::getToolbar($toolbar, $config);
	}	
}