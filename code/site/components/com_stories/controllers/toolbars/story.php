<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Stories Toolbar
 *
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesControllerToolbarStory extends ComBaseControllerToolbarDefault
{ 
    /**
     * Set the list commands
     *
     * @param KConfig $data Data
     *
     * @return void
     */
    public function addListCommands(KConfig $data)
    {
        $story = $data->entity;
        
        if ( $story->authorize('vote') )
        {
            $entity = $story->hasObject() ? $story->object : $story;             
        
            if ( !is_array($entity) )
                $this->addCommand('vote', array('entity'=>$entity));
        }
        
        $commentable = $story->authorize('add.comment');
        
        if ( $commentable !== false ) {
            if ( $story->hasObject() && is_array($story->object) )
                $commentable = false;
        }
        
        if( $commentable ) {
            $this->addCommand('comment', array('entity'=>$story));
        }
        
        if ( $story->numOfComments > 10 ) {
            $this->addCommand('view', array('entity'=>$story));
        }
        
        if( $story->authorize('delete') )
            $this->addCommand('delete', array('entity'=>$story));
    }
    
    /**
     * View Stories
     *
     * @param LibBaseTemplateObject $command The command object
     *
     * @return void
     */
    protected function _commandView($command)
    {
        $entity = $command->entity;
        $label = sprintf( JText::_('COM-STORIES-VIEW-ALL-COMMENTS'), $entity->getNumOfComments());
        $command->append(array('label'=>$label));
        $command->href($entity->getURL());
    }
     
    /**
     * Comment command
     *
     * @param LibBaseTemplateObject $command The command object
     *
     * @return void
     */
    protected function _commandComment($command)
    {
        $entity = $command->entity;
        
        $command->append(array('label'=>JText::_('LIB-AN-ACTION-COMMENT')))
            ->href($entity->getURL())
            ->class('comment')
            ->storyid($entity->id);     
    }   
     
    /**
     * Delete Command for a story
     *
     * @param LibBaseTemplateObject $command The command object
     *
     * @return void
     */
    protected function _commandDelete($command)
    {
        $entity = $command->entity;
    
        $command->append(array('label'=>JText::_('LIB-AN-ACTION-DELETE')))
        ->href($entity->getStoryURL(true).'&action=delete')
        ->setAttribute('data-trigger','Remove');
    }
        
}