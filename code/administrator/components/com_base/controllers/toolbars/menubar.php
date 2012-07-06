<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Resource Controller
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerToolbarMenubar extends ComDefaultControllerToolbarMenubar
{
    /**
     * Push the toolbar into the view
     * .
     * @param	KEvent	A event object
     */
    public function onAfterControllerGet(KEvent $event)
    {
         //Render the menubar
        $document = JFactory::getDocument();
        $config   = array('menubar' => $this);
    
        $menubar = $event->getPublisher()->getView()->getTemplate()->getHelper('menubar')->render($config);
        $document->setBuffer($menubar, 'modules', 'submenu');
    } 

    /**
     * Push the toolbar into the view
     * .
     * @param	KEvent	A event object
     */
    public function onAfterControllerBrowse(KEvent $event)
    {
        $this->addParameterCommand();        
    }

    /**
     * Sets the component parameter in the menu bar
     *
     * @return void
     */
    public function addParameterCommand()
    {
        $file = JPATH_COMPONENT.DS.'config.xml';
        $user = JFactory::getUser();
        if ( file_exists($file) && $user->usertype == 'Super Administrator' )
        {
            if ( count($this->getCommands()) > 0 )
            {
                $this->addCommand('configurations',
                        array('href'=>JRoute::_('index.php?option=com_'.$this->getIdentifier()->package.'&view=configurations')));                
            }
            
            //if the view is configuration then just add a save
            if ( $this->getController()->view == 'configurations' ) 
            {
                $toolbar = $this->getController()->getIdentifier()->name;
                
                if ( $this->getController()->hasToolbar($toolbar) )
                {
                    $this->getController()->getToolbar($toolbar)->addCommand('save');
                }
            }
        }
    }    
}