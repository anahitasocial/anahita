<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Entityset
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * One to many aggregated entityset
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Entityset
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainEntitysetOnetomany extends AnDomainEntitysetDefault
{	
	/**
	 * The aggregate root
	 * 
	 * @var AnDomainEntityAbstract
	 */
	protected $_root;	
	
	/**
	 * Child property in the many set
	 * 
	 * @var string
	 */
	protected $_property;
	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		$this->_root 	 = $config->root;
		
		$this->_property = $config->property;
		
		parent::__construct($config);
	}
		
	/**
	 * Return an entity of the aggregated type and set the initial 
	 * property
	 * 
	 * @param  array $data
	 * @param  array $config Extra configuation for instantiating the object
	 * @return AnDomainEntityAbstract
	 */
	public function findOrCreate($data = array(), $config = array())
	{		
		$entity = $this->find($data);
		
		if ( !$entity ) {
			$entity = $this->create($data, $config);
		}
		
		return $entity;
	}
		
	/**
	 * Find an entity with the passed condition
	 * 
	 * @param  array $conditions
	 * @return AnDomainEntityAbstract 
	 */
	public function find(array $conditions)
	{
		$conditions[$this->_property] = $this->_root;	
			
		$found = $this->getRepository()->find($conditions);
			
		return $found;
	}
		
	/**
	 * Return an entity of the aggregated type and set the initial 
	 * property
	 * 
	 * @param  array $data
	 * @param  array $config Extra configuation for instantiating the object
	 * @return AnDomainEntityAbstract
	 */
	public function create($data = array(), $config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'data' => $data
		));
		$entity = $this->getRepository()->getEntity($config);
		$this->insert($entity);
		return $entity;
	}
	
	/**
	 * Insert an entity to the aggregation
	 * 
	 * @see KObjectSet::insert()
	 */
	public function insert($entity)
	{
		$entity->set($this->_property,$this->getRoot());
    	return parent::insert($entity);
	}

	/**
	 * Removes an object from the aggregation
	 * 
	 * @see KObjectSet::extract()
	 */
    public function extract($entity)
    {
    	//if entity is required then delete the entity
    	$property  = $this->getRepository()->getDescription()->getProperty($this->_property);
    	if ( $property->isRequired() ) 
    	{
    		$entity->delete();    		
    	} else 
    	{
    		$entity->set($this->_property,null);
    	}
    }    
	
	/**
	 * Return the aggregate root
	 * 
	 * @return AnDomainEntityAbstract
	 */
	public function getRoot()
	{
		return $this->_root;
	}    
}