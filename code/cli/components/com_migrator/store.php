<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita_Dev
 * @package    Com_Migrator
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Abstract Controller
 *
 * @category   Anahita_Dev
 * @package    Com_Migrator
 * @subpackage Store
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComMigratorStore extends KObject
{
    /**
     * Return the instance of the migrator store
     *
     * @return ComMigratorStore
     */
    static public function getInstance()
    {
        static $instance;
        
        if ( !$instance )
        {
            $instance = new ComMigratorStore();            
        }
        
        return $instance;
    }
    
    /**
     * Entity
     * 
     * @var string
     */
    protected $_entity;
    
    /**
     * Versions
     *
     * @var KConfig
     */
    protected $_versions;    
    
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config = null)
    {        
        $this->_versions = $this->_loadVersions();       
    }
    
    /**
     * Return the version of a name
     * 
     * @param string $name The name of the component
     * 
     * @return int
     */
    public function getVersion($name)
    {
        return pick($this->_versions->$name, 0);
    }
    
    /**
     * Deletes a migration
     *
     * @return void
     */
    public function delete($name)
    {
        if ( isset($this->_versions[$name]) )
        {
            unset($this->_versions[$name]);
            $this->save();            
        }
    }
    
    /**
     * Return the version of a name
     *
     * @param string $name    The name of the component
     * @param int    $version Store a version for a component
     * 
     * @return int
     */
    public function setVersion($name, $version)
    {
        $this->_versions[$name] = $version;
        return $this;
    }  

    /**
     * Store the version
     *
     * @return void
     */
    protected function _loadVersions()
    {
        /*
       $db  = KService::get('koowa:database.adapter.mysqli');
       $sql = <<<EOF
        CREATE TABLE IF NOT EXISTS `#__migrator_migraitons` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `migrations` TEXT NULL DEFAULT '',
          PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;
EOF;
        $db->execute($sql);
       */
        $entity = AnDomain::getRepository('com:migrator.domain.entity.migraiton')->fetch();
        if ( !$entity ) {
            $entity = AnDomain::getRepository('com:migrator.domain.entity.migraiton')->getEntity();
            $entity->setData(array('migrations'=>''));
            $entity->save();
        }        
        $this->_entity = $entity;
        $reg = new JRegistry();
        $reg->loadINI($this->_entity->migrations);
        return new KConfig($reg->toArray());
    }
        
    /**
     * save all the version
     *
     * @return void
     */
    public function save()
    {
        $reg = new JRegistry();
        $reg->loadArray((array)KConfig::unbox($this->_versions));
        $this->_entity->migrations = $reg->toString();
        $this->_entity->save();
    }
}