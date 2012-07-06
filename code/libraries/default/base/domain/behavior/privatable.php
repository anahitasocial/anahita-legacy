<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Privatable Behavior
 * 
 * Provides privacy for nodes
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDomainBehaviorPrivatable extends AnDomainBehaviorAbstract
{
	/**
	 * Graph Privacy Constants
	 */
	const GUEST 	   = 'public';
	const REG		   = 'registered';
	const SPECIAL	   = 'special'; //special permission
	const FOLLOWER     = 'followers';
	const LEADER	   = 'leaders';
	const MUTUAL	   = 'mutuals';
	const ADMIN	   	   = 'admins';
		
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
		$config->append(array(
			'attributes' => array(
				'access'			=> array('default'=>self::GUEST),
				'permissions'		=> array('type'=>'json','default'=>'json')
			)
		));
		
		parent::_initialize($config);
	}	
	
	/**
	 * Set the access value of the node. Access value is basically a read persmission for a node. 
	 * This value can be checked during fetching the record from the database to see if the 
	 * viewer has access to it or not
	 * 
	 * @param int|string $access Access value. Can be a string or an id of another node
	 * 
	 * @return LibBaseDomainBehaviorPrivatable
	 */
	public function setAccess($value)
	{
		$values = (array) $value;
		
		foreach($values as $key => $value) 
		{
			if ( empty($value) )
				$value = self::GUEST;
			elseif ( !is_numeric($value) ) 
			{
				if ( !in_array($value, array('public','registered','followers','special','leaders','mutuals','admins')) ) {
					$value = 'public';
				}
			}
			if ( $value == self::GUEST ) {
				$values = array(self::GUEST);
				break;
			}
            
            if ( $this->_mixer->authorize('setprivacyvalue', array('value'=>$key)) === false ) {
                unset($values[$key]);
            }
			else 
                $values[$key] = $value;
		}
		
		asort($values);
		
		$values = array_unique($values);
		
		$this->set('access', implode(',', $values));

		return $this;
	}
	
	/**
	 * Return a permission value for of a resource. If the permission doesn't exists then it returns the default
	 * value
	 * 
	 * @param string $key     The name of the resource
	 * @param mixed  $default The default value, if the permission is not set yet 
	 * 
	 * @return mixed
	 */
	public function getPermission($key, $default = self::GUEST)
	{
		if ( $key == 'access' )
			return $this->access;
							
		return $this->permissions->get($key, $default);
	}
	
	/**
	 * Sets a permission for a resource
	 * 
	 * @param string     $key The name of the resource
	 * @param int|string $value The value of the permission. Can be string or integer
	 * 
	 * @return LibBaseDomainBehaviorPrivatable
	 */
	public function setPermission($key, $value)
	{
		if ( empty($value) )
			$value = self::GUEST;
					
		if ( $key == 'access' )
			$this->access = $value;
		else 
		{
			$permission 	  = clone $this->permissions;
			$permission[$key] = $value;
			$this->set('permissions', $permission);
		}
		
		return $this;
	}
	
	/**
	 * Return whether $accessor has privellege perform $operation on the entity 
	 * 
	 * @param  ComPeopleDomainEntityPerson $accessor  The person who's trying to perform an operation on the entity
	 * @param  string	 	               $operation The name of the operation being performed
	 * @param  string                      $default   The default value to use if the there are no values are set for the operation
	 * 
	 * @return boolean
	 */
	public function allows($accessor, $operation, $default = LibBaseDomainBehaviorPrivatable::REG)
	{
        //keep a reference to mixer just in case
        $mixer = $this->_mixer;
        
		$actor = null;
		
		if ( is($this->_mixer, 'ComActorsDomainEntityActor') )
			$actor = $this->_mixer;
		elseif ( $this->isOwnable() ) {
			$actor = $this->owner;
		}
		
        if ( !empty($actor) ) 
        {
            //no operation is allowed if the actor is blokcing the $accessor
            if ( $actor->isFollowable() && $actor->blocking($accessor) )
                return false;
            
            //no opreation is allowed if actor is not published and the accessor
            //is not admin
            if ( $actor->isEnableable() && $actor->enabled === false ) {
                if ( $accessor->isAdministrator() && !$accessor->administrator($actor) )
                    return false;
            }
        }
        
        //an array of entities whose permission must return true 
        $entities = array();
        
        if ( !empty($actor) ) 
            $entities[] = $actor;
         
        if ( !in_array($mixer, $entities) )
            $entities[] = $mixer;     
      
        foreach($entities as $enttiy)
        {
            $permissions = explode(',', $enttiy->getPermission($operation, $default));            
            $result      = $this->checkPermissions($accessor, $permissions, $actor);
            if ( $result === false )
                return false;               
        }
          
        return true;
	}
    
    /**
     * Checks an array of permissions against an accessor using the socialgraph between the
     * accessor and actor
     * 
     * @param ComActorsDomainEntityActor $accessor     The actor who's trying to perform an operation
     * @param array                      $permissions  An array of permissions
     * @param ComActorsDomainEntityActor $actor        If one of the permision
     * 
     * @return boolean
     */
    public function checkPermissions($accessor, $permissions, $actor)
    {        
        $result = true;
        
        //all must be false in order to return false
        foreach($permissions as $value)
        {
            $value  = pick($value, LibBaseDomainBehaviorPrivatable::GUEST);
            
            switch($value)
            {
                //public
                case LibBaseDomainBehaviorPrivatable::GUEST :
                    $result = true;
                    break;
                //registered
                case LibBaseDomainBehaviorPrivatable::REG :                         
                    $result = !$accessor->guest();
                    break;
                //follower
                case LibBaseDomainBehaviorPrivatable::FOLLOWER :
                    $result = $accessor->following($actor) || $accessor->leading($actor) || $accessor->administrator($actor);
                    break;
                //leader
                case LibBaseDomainBehaviorPrivatable::LEADER :
                    $result = $accessor->leading($actor) || $accessor->administrator($actor);
                    break;
                //mutual                        
                case LibBaseDomainBehaviorPrivatable::MUTUAL :
                    $result = $accessor->mutuallyLeading($actor) || $accessor->administrator($actor);
                    break;
                case LibBaseDomainBehaviorPrivatable::ADMIN :
                    $result = $accessor->administrator($actor);
                    break;
                default : 
                     $result = $accessor->id == $value;
            }
            
            if ( $result === true ) {                
                break;
            }
        }

        return $result;
      
    }
	
	/**
	 * Creates a where statement that checks actor id and access column values against viewer socialgraph and viewer id
	 *
	 * @param string  $actor_id The name of the columm containing actor ids. 
	 * @param KConfig $config   Configuration parameter
	 * @param string  $access   The name of the column containing access values
	 * 
	 * @return string
	 */
	protected function _createWhere($actor_id, $config ,$access = '@col(access)')
	{
	    $store      = $this->_repository->getStore();
		$viewer 	= $config->viewer;
		$where[] = "CASE";
		$where[] = "WHEN $access IS NULL THEN 1";
		
		if (  false && $config->visible_to_leaders && $viewer->id && count($viewer->blockedIds) > 0 )
		{
		    $where[] = "WHEN FIND_IN_SET(@col(id), '".$store->quoteValue($viewer->blockedIds->toArray())."') THEN 1";
		}
		
		$where[] = "WHEN FIND_IN_SET('".self::GUEST."', $access) THEN 1";
		if ( $viewer->id )
		{
			$where[] = "WHEN FIND_IN_SET('".self::REG."',$access) THEN 1";
			if ( $viewer->userType != 'registered')
				$where[] = "WHEN FIND_IN_SET('".self::SPECIAL."',$access) THEN 1";
			$where[] = "WHEN FIND_IN_SET(".$viewer->id.",$access) THEN 1";
			if ( $config->graph_check )
			{
				$leader_ids	  = $store->quoteValue($viewer->leaderIds->toArray());
				$follower_ids = $store->quoteValue($viewer->followerIds->toArray());
				$mutual_ids   = $store->quoteValue($viewer->mutualIds->toArray());
				$admin_ids	  = $store->quoteValue($viewer->administratingIds->toArray());
				$is_viewer    = "$actor_id = {$viewer->id}";
				$viewer_is_follower   = "$is_viewer  OR $actor_id IN (".$leader_ids.")";
				$viewer_is_leader     = "$is_viewer  OR $actor_id IN (".$follower_ids.")";				
				$viewer_is_mutual     = "$is_viewer  OR $actor_id IN (".$mutual_ids.")";
				$viewer_is_admin	  = "$is_viewer  OR $actor_id IN (".$admin_ids.")";
				$requestable          = $this->_repository->isFollowable() ? "@col(allowFollowRequest) = 1" : "FALSE";	 	
                //if privacy set to follow, show the actor only if viewer is a follower or viewer is a leader or actor 
                //accecpts follow request
				$where[] = "WHEN FIND_IN_SET('".self::FOLLOWER."',$access) THEN $viewer_is_follower OR $viewer_is_leader OR $requestable";
				$where[] = "WHEN FIND_IN_SET('".self::LEADER."',$access) THEN $viewer_is_leader";
				if ( $config->visible_to_leaders ) 
				{
					$where[] = "WHEN FIND_IN_SET('".self::MUTUAL."',$access) THEN $viewer_is_leader";
					$where[] = "WHEN FIND_IN_SET('".self::ADMIN."',$access) THEN $viewer_is_admin OR $viewer_is_leader";
				} 
				else
				{ 
				    $where[] = "WHEN FIND_IN_SET('".self::MUTUAL."',$access) THEN $viewer_is_mutual";
				    $where[] = "WHEN FIND_IN_SET('".self::ADMIN."',$access) THEN $viewer_is_admin";
				}
				    
								
			}
		}
		$where[] = "ELSE 0";
		$where[] = "END";
		return implode(' ', $where);	
	}
}