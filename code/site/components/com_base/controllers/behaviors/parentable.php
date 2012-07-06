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
 * Parentable Behavior
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerBehaviorParentable extends KControllerBehaviorAbstract
{
   
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
		
		if ( $parts[0] == 'before' && !$context->parent_fetched ) 
		{
			$context->parent_fetched = true;
			
			$request = $this->getRequest();
			
			$data	 = $context->data;
			
			if ( !array_key_exists('pid', KConfig::unbox($data)) &&
				 !array_key_exists('pid', KConfig::unbox($request)) 
			){
				$request->append(array(
					'pid' => $data->pid
				));				
				return;
			}
			
			$request->append(array(
				'pid' => $data->pid
			));
			
			$parent  = $this->getParent();
			$data	 = $context->data;
			
			//reserve parent 
			$data->parent = null;
			
			if ( $request->pid && $parent ) 
			{
				$repository = $this->getParentRepository();
				$scope		= array('id'=>$request->pid);
				if ( $repository->hasBehavior('ownable') && $data->actor ) 
					$scope['owner'] = $data->actor;
				$data->{$parent->name} = $data->parent = $repository->fetch($scope);
			}
		
			
		}
    } 
    
    /**
     * Return the parent repository
     *
     * @return AnDomainRepositoryAbstract
     */
    public function getParentRepository()
    {
        $parent 	= $this->getIdentifier($this->getParent());
        return AnDomain::getRepository($parent);        
    }
    
    /**
     * Return the parent identifier
     * 
     * @return string 
     */
    public function getParent()
    {
    	$parent = $this->getRepository()->getDescription()->getProperty('parent');
    	return $parent->getParent();
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