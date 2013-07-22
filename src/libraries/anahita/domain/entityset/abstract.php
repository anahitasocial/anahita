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
 * Abstract Domain Entityset
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Entityset
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class AnDomainEntitysetAbstract extends AnObjectSet
{	
	/**
	 * Repository
	 * 
	 * @var AnDomainRepositorAbstract 
	 */
	protected $_repository;

	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{				
		$this->_repository = $config->repository;
		
		parent::__construct($config);		
	}

    /**
	 * If the method has a format is[A-Z] then it's a behavior name
	 * 
	 * @param string $method
	 * @param array  $arguments
	 * @return mixed
	 */
	public function __call($method, $arguments = array())
	{
        $parts = KInflector::explode($method);
        
		if ( $parts[0] == 'is' && isset($parts[1]) ) 
		{
			$behavior = lcfirst(substr($method, 2));
			return !is_null($this->_repository->getBehavior($behavior));
		}
				
		return parent::__call($method, $arguments);
	}   
	 
	/**
	 * Finds an entity within the entityset the matches the criteria. If $set
	 * is passed then it finds a set
	 *
	 * @param  array|string $needle
	 * @param  boolean $set
	 * @return AnDomainEntityAbstract|null
	 */
	public function find($needle, $set = false)
	{
		if ( $needle instanceof KObjectHandlable ) {
			return parent::find($needle);	
		}
		
		$entities = array();
		
		foreach($this as $entity) 
 		{
 			foreach($needle as $key => $value) 
 			{
 				$v		= AnHelperArray::getValue($entity, $key);
 				if ( is($value,'AnDomainEntityAbstract') || 
 					 is($value,'AnDomainEntityProxy') ) { 
 					$is_equal = $value->eql($v);
 				} else 
 					$is_equal = $value == $v;
 				if ( !$is_equal ) {
 					break;
 				}
 			}
 			if ( $is_equal ) {
 				if ( $set )
 					$entities[] = $entity;
 				else return $entity;
 			}
 		}
 		
 		if ( !$set )
 			return null;
 		return new AnDomainEntityset(new KConfig(array('data'=>$entities, 'repository'=>$this->_repository)));
	}
		
	/**
	 * Inspects the entityset
	 * 
	 * @param  boolean $dump
	 * @return array;
	 */
	public function inspect($dump = true)
	{
		$data = array();
		
		foreach($this as $entity) 
		{
			$data[] = $entity->inspect(false);
		}
		if ( $dump ) {
			var_dump($data);
		} else
			return $data;
	}
		
	/**
	 * Return the entityset repository
	 * 
	 * @return AnDomainAbstractRepository
	 */
	public function getRepository()
	{
		return $this->_repository;
	}
    
    /**
     * Return the entityset an an array of entities
     * 
     * @return array
     */
    public function toArray()
    {
        $array = array();
        
        foreach($this as $entity) {
            $array[] = $entity;   
        }
        
        return $array;
    }
        
	/**
     * Retrieve an array of column values and return an array of
	 * objects, scarlar or a single boolean value
     *
     * @param  	string 	The column name.
     * @return 	mixed 	
     */
    public function __get($column)
    {
    	$return 	 = null;
    	
        $description = $this->getRepository()->getDescription();
        
    	if ( $property = $description->getProperty($column) ) 
        {
    		if ( $property->isAttribute() ) 
            {
                if ( $property->isScalar() )
                {
                    $return = $property->getType() == 'boolean' ? 'boolean' : 'array';
                }
    		}            
    	}
 		return $this->_forward('attribute', $column, array(), $return);
    }		
}