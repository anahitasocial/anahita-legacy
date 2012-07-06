<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Actorbar. 
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsControllerToolbarActorbar extends ComBaseControllerToolbarActorbar
{
    /**
     * Set the header for the default actor
     *
     * @todo very large function perhaps it should be done as command chain
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainEntitysetDefault
     */
    public function onBeforeControllerGet(KEvent $event)
    {
        parent::onBeforeControllerGet($event);
    
        $data 	= $event->data;
        $viewer = get_viewer();
        $actor	= pick($data->actor, $viewer);
        $layout = pick($this->getController()->layout, 'default');
        $name	= $this->getController()->getIdentifier()->name;
    
        //if vieweing one actor
        if ( $this->getController()->isIdentifiable() && $data->entity && $data->entity->isDescribable() )
        {
            $this->setActor(null);
            
            //viewing one's socialgraph
            if ( $this->getController()->get == 'graph' )
            {
                $types = array();
    
                if ( $data->entity->isFollowable() ) {
                    $types[] = 'Followers';
                }
    
                if ( $data->entity->isLeadable() ) {
                    $types[] = 'Leaders';
                    $types[] = 'Mutuals';
                    if ( !$data->entity->eql( get_viewer() ) ) {
                        $types[] = 'CommonLeaders';
                    }
                }
    
                if ( $data->entity->authorize('administration', array('strict'=>true)) ) {
                    $types[] = 'Blockeds';
                }

                foreach($types as $type)
                {
                    $label	 = array(strtoupper('COM-'.$this->getIdentifier()->package.'-NAV-LINK-SOCIALGRAPH-'.$type));
                    $label[] = 'COM-ACTORS-NAV-LINK-SOCIALGRAPH-'.strtoupper($type);
                    $cmd	 = strtolower($this->getController()->sanitize($type, 'cmd'));
                    $this->addNavigation('navbar-'.$cmd,translate($label),
                            $data->entity->getURL().'&get=graph&type='.$cmd,
                            $this->getController()->type == $cmd);
                }
    
                $title  	= array(strtoupper('COM-'.$this->getIdentifier()->package.'-NAV-TITLE-SOCIALGRAPH'));
                $title[] = 'COM-ACTORS-NAV-TITLE-SOCIALGRAPH';
                $this->setTitle(sprintf(translate($title), $data->entity->name));
                $this->setActor($data->entity);
            }
            else
                $this->setTitle($data->entity->getName());
        }
        //if viewing a list of actors related to another actor
        elseif ( $this->getController()->isOwnable() && $data->actor )
        {
            $filters = array('following');
    
            if ( $this->getController()->getRepository()->hasBehavior('administrable') ) {
                $filters[] = 'administering';
            }
    
            $this->setActor($data->actor);
    
            $type  		= ucfirst(KInflector::pluralize($this->getController()->getIdentifier()->name));
            foreach($filters as $filter)
            {
                //COM-[COMPONENT]-NAV-FILTER-[ADMINISTRATING|FOLLOWING]
                $label = array(strtoupper('COM-'.$this->getIdentifier()->package.'-NAV-FILTER-'.$filter));
                if ( $filter == 'following' ) {
                    $label[] = 'Following';
                } elseif ( $filter == 'administering' ) {
                    $label[] = 'Administering';
                }
                $this->addNavigation('navbar-'.$filter,translate($label),
                        array('option'=>$this->getController()->option,'view'=>$this->getController()->view,'oid'=>$data->actor->id,'filter'=>$filter),
                        $this->getController()->filter == $filter);
            }
    
            $filter = pick($this->getController()->filter, 'following');
            $title  = array(strtoupper('COM-'.$this->getIdentifier()->package.'-NAV-TITLE-'.$this->getController()->filter));
    
            if ( $filter == 'following' ) {
                $title[] = $type.'COM-GROUPS-NAV-TITLE-FOLLOWING';
            } elseif ( $filter == 'administering' ) {
                $title[] = $type.'COM-GROUPS-NAV-TITLE-ADMINISTERING';
            }
    
            $this->setTitle(sprintf(translate($title), $data->actor->name));
        }
    }    
}