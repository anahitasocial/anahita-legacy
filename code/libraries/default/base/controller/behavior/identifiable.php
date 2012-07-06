<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Identifiable Behavior
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseControllerBehaviorIdentifiable extends KControllerBehaviorAbstract
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
		
		if ( $parts[0] == 'before' && !$context->entity_fetched ) 
		{
			$context->entity_fetched = true;
			return $this->_mixer->fetchEntity($context);
		}
    }
    
 	/**
     * Fetches an entity
     *
     * @param KCommandContext $context
     */
    public function fetchEntity(KCommandContext $context)
    {
    	$context->append(array(
    		'identity_scope' => array()
    	));
    	
    	$request = $this->getRequest();
    	$data    = $context->data;
    			
		$id_key  = $this->getRepository()->getDescription()->getIdentityProperty()->getName();
		if ( isset($request->$id_key) )
			$data->append(array('id'=>$request->$id_key));
		
		if ( isset($data->$id_key) ) 
		{
			$data   = $context->data;
			
			$value	= KConfig::unbox($data->$id_key);
			
			if ( is_array($value) ) 
				$mode = AnDomain::FETCH_ENTITY_SET;
			else
				$mode = AnDomain::FETCH_ENTITY;

			$scope			= KConfig::unbox($context->identity_scope);			
			$scope[$id_key] = $value;
			
			$entity = $this->getRepository()->fetch($scope, $mode);
            
			if ( empty($entity) || !count($entity)) {
                $context->setError(new KHttpException(
            		'Resource Not Found', KHttpResponse::NOT_FOUND
                ));
                return false;						
			}
			
			$name = $this->_mixer->getEntity()->name;
			$data->$name  = $entity;
			$data->entity = $entity;
			return $entity;
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