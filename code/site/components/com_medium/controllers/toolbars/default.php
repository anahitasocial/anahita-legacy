<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Default Medium Controller Toolbar
 *
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComMediumControllerToolbarDefault extends ComBaseControllerToolbarDefault
{    
    /**
     * Called after controller browse
     *
     * @param KEvent $event
     *
     * @return void
     */
    public function onAfterControllerBrowse(KEvent $event)
    {
        $data   = $event->data;
        $actor  = $data->actor;
        $filter = $this->getController()->filter;
        
        if ( $this->getController()->canAdd($data) && $filter != 'leaders' ) 
        {
            $this->addCommand('new', array('actor'=>$actor));
        }        
    }
        
    /**
     * Set the toolbar commands
     *
     * @param KConfig $data Data
     *  
     * @return void
     */
    public function addToolbarCommands(KConfig $data)
    {
        $entity = $data['entity'];
        
        if ( $entity->authorize('vote') )
            $this->addCommand('vote', array('entity'=>$entity));
                
        if(	$entity->authorize('subscribe') || ( $entity->isSubscribable() && $entity->subscribed(get_viewer())))
            $this->addCommand('subscribe', array('entity'=>$entity));
                
        if ( $entity->authorize('edit') )
            $this->addCommand('edit', array('entity'=>$entity));
        
        if ( $entity->isOwnable() && $entity->owner->authorize('administration') )
            $this->addAdministrationCommands($data);       
        
        if ( $entity->authorize('delete') )
            $this->addCommand('delete', array('entity'=>$entity));        
    }
     
    /**
     * Called before list commands
     *
     * @param KConfig $data Data
     *
     * @return void
     */
    public function addListCommands(KConfig $data)
    {
        $entity = $data->entity;
        
        if ( $entity->authorize('vote') ) {
            $this->addCommand('vote', array('entity'=>$entity));
        }
        
        if ( $entity->authorize('delete') ) {
            $this->addCommand('delete', array('entity'=>$entity));
        }
    } 

    /**
     * Add Admin Commands for an entity
     *
     * @param KConfig $data Data
     *  
     * @return void
     */
    public function addAdministrationCommands(KConfig $data)
    {
        $entity = $data->entity;
        
        if ( $entity->isOwnable() && $entity->owner->authorize('administration') )
        {
            if ( $entity->isEnablable() )
                $this->addCommand('enable', array('entity'=>$entity));
    
            if ( $entity->isCommentable() )
                $this->addCommand('commentstatus', array('entity'=>$entity));
        }
    }

    /**
     * New button toolbar
     *
     * @param LibBaseTemplateObject $command The action object
     *
     * @return void
     */
    protected function _commandNew($command)
    {
        $actor  = $command->actor;
        $name   = $this->getController()->getIdentifier()->name;
        $labels = array();
        $labels[] = strtoupper('com-'.$this->getIdentifier()->package.'-toolbar-'.$name.'-new');
        $labels[] = 'New';
        $label = translate($labels);
        $url   = 'option=com_'.$this->getIdentifier()->package.'&view='.$name.'&oid='.$actor->id.'&layout=add';
        $command->append(array('label'=>$label))
                ->href($url);
    }
}