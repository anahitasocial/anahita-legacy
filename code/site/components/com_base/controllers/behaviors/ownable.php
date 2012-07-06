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
 * Ownable Behavior. It feches an owner wherenever there's an oid
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerBehaviorOwnable extends KControllerBehaviorAbstract
{
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority'   => KCommand::PRIORITY_HIGHEST
        ));

        parent::_initialize($config);
    }
		
	/**
     * Command handler
     * 
     * @param   string      The command name
     * @param   object      The command context
     * @return  boolean     Can return both true or false.  
     */
    public function execute($name, KCommandContext $context) 
    {
		$parts = explode('.', $name);
		
		if ( $parts[0] == 'before' ) 
		{
			return $this->_fetchOwner($context);
		}
    }
   	
    /**
     * Fetches an entity
     *
     * @param KCommandContext $context
     */
    protected function _fetchOwner(KCommandContext $context)
    {
		$request = $this->getRequest();
		$data	 = $context->data;
		
    	//fetch actor
		if ( isset($request->oid) ) 
		{
			if ( $request->oid == 'viewer' && !get_viewer()->guest() ) 
			{
				$actor  = get_viewer(); 
			}
			else 
			{ 
			    $actor = $this->getService('repos:actors.actor')->fetch((int)$request->oid);
			}
        
			//actor not found
			if ( !$actor ) {
                $context->setError(new KHttpException(
            		'Owner Not Found', KHttpResponse::NOT_FOUND
                ));
                return false;
			}
			
			$data->owner = $data->actor = $actor;
			
			return $actor;
		}
    }
    	       	
    /**
     * Return the object handle
     * 
     * @return string
     */
    public function getHandle()
    {
    	return KMixinAbstract::getHandle();
    }
}