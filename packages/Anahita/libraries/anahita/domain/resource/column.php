<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Resource
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * A resource column represent an atomic data for a resource
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Resource
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainResourceColumn 
{
	/**
	 * Column name
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * Resource name the column belongs to
	 * 
	 * @var AnDomainResourceInterface
	 */
	public $resource;

	/**
	 * Column default value
	 * 
	 * @var string
	 */	
	public $default;
		
	/**
	 * Columns type
	 * 
	 * @var string
	 */
	public $type;

	/**
	 * Required column
	 * 
	 * @var bool
	 */
	public $required = false;

	/**
	 * Is the column unqiue
	 * 
	 * @var	bool
	 */
	public $unique = false;
		
	/**
	 * Is the column a primary column
	 * 
	 * @var boolean
	 */
	public $primary = false;
	
	/**
	 * Return a string represntation of a column
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->resource->getAlias().'.'.$this->name;
	}
	
	/**
	 * Return the key for the returned data
	 * 
	 * @return string
	 */
	public function key()
	{
		if (  $this->resource->getLink() ) 
		{
			return (string) $this;
		}
		return $this->name;
	}
	
	/**
	 * Clones a column
	 * 
	 * @return void
	 */
	public function __clone()
	{
	    if ( $this->resource )
	    {
	        $this->resource = clone $this->resource;
	    }
	}
	
}