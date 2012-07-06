<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Restful Controller Viewer
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseControllerResource extends LibBaseControllerView
{
    /**
     * Conroller entity identifier
     *
     * @var string
     */
    protected $_entity;
    
    /** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */    
    public function __construct(KConfig $config)
    {
        parent::__construct($config);        
                
        $this->setEntity($config->entity);
        
        if ( $config->commitable ) 
        {
            $this->addBehavior('committable');
            $this->addBehavior('loggable');
        }
    }
        
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
        $identifier       = clone $this->getIdentifier();    
        $identifier->path = array('domain', 'entity');
        
        $config->append(array(
            'commitable'    => true,        
            'entity'        => $identifier             
        ));
              
        parent::_initialize($config);
        
        if ( !$config->entity ) 
            $config->commitable = false;
    }
    
    /**
     * Set the entity of the controller
     * 
     * @param string $entity Thde entity to set
     * 
     * @return LibBaseControllerResource
     */
    public function setEntity($entity)
    {
        if (is_string($entity) && strpos($entity, '.') == 0) 
        {
             $identifier = clone $this->getIdentifier();
             $identifier->path = array('domain', 'entity');
             $identifier->name = $entity;
             $entity = $identifier;
        }

        $entity = $this->getIdentifier($entity);
        
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Return the controller entity identifier
     * 
     * @return KServiceIdentifier
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Return the controller repository based on the entity. If an 
     * identifier is passed it wil return the repository
     * of the identifier
     * 
     * @param string $identifier The identifier of the repository
     * 
     * @return AnDomainRepositoryAbstract
     */
    public function getRepository( $identifier = null)
    {
        $identifier = pick($identifier, $this->getEntity());

        return parent::getRepository($identifier);
    }
}