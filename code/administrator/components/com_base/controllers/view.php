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
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * View Controller (Resourceless)
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerView extends LibBaseControllerView
{
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
		    'toolbars'    => array('menubar', $this->getIdentifier()->name),		        
			'behaviors'   => array(
				'executable'
			)
		));
		
		parent::_initialize($config);
	}
	
	/**
	 * Controller View can't add
	 * 
	 * @return boolean
	 */
	public function canAdd()
	{
		return false;
	}
	
	/**
	 * Controller View can't delete
	 * 
	 * @return boolean
	 */
	public function canDelete()
	{
		return false;
	}	
}