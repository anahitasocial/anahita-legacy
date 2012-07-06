<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Apps
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * App Controller
 *
 * @category   Anahita
 * @package    Com_Apps
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComAppsControllerApp extends ComBaseControllerResource 
{	
	/**
	 * Assign apps to an actor
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _actionSave($context)
	{
		$data = $context->data;
		$data->app->assignTo(KConfig::unbox($data->actors));
		$this->setRedirect('index.php?option=com_apps&view=apps');	
	}
		
	/**
	 * Assign apps to an actor
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _actionApply($context)
	{
		$data = $context->data;
		$data->app->assignTo(KConfig::unbox($data->actors));	
	}
	
	/**
	 * Return an app
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _actionRead($context)
	{
		$data     = $context->data;
		$entity   = $data->entity;
        //can't assign to an always app
        if ( $entity->getAssignmentOption() == ComAppsDomainDelegateDefault::ASSIGNMENT_OPTION_ALWAYS ||
             $entity->getAssignmentOption() == ComAppsDomainDelegateDefault::ASSIGNMENT_OPTION_NEVER
             )
             return false;
        $this->getService('koowa:loader')->loadIdentifier('com://admin/apps.domain.model.app');
		$data->actors = ComAppsDomainModelApp::getActorIdentifiers($entity);
	}
	
	/**
	 * Returns a list of apps
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _actionBrowse($context)
	{
		ComAppsDomainModelApp::syncApps();
		$actors = ComAppsDomainModelApp::findActorIdentifiers();
        $components = array();
        
        foreach($actors as $actor) {
            $components[] = 'com_'.$actor->package;    
        }
       
		$entities   = parent::_actionBrowse($context);
        
        $this->getBehavior('executable')->setReadOnly(true);
        
        //the always must be null
        $entities->where('@col(always) IS NULL');
        
        if ( count($components) && false ) {
            $entities->where('@col(component) IN (:components)', 'OR')->bind('components',$components);   
        }
        
		//$components
		$this->getToolbar('app')->setTitle('App Assignments');
						
		return $entities;
	}	
}