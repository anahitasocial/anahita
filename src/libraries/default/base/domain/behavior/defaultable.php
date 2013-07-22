<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
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
 * Defultable Behavior. 
 * 
 * Allows to set an entity as the default entity within a set of entities
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDomainBehaviorDefaultable extends AnDomainBehaviorAbstract
{

    /**
     * An array of key/value conditions from which to set a scope
     * 
     * @var array
     */
    protected $_scope;
    
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
        
        $this->_scope = KConfig::unbox($config->scope);
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
		$config->append(array(
		    'scope'      => array(),
			'attributes' => array(
				'isDefault'=>array(
					'default' => false
				))
		));
		
		parent::_initialize($config);
	}
	
	/**
	 * Set the order before inserting
	 *
	 * @param  KCommandContext $context
	 * @return void
	 */
	protected function _beforeEntityInsert(KCommandContext $context)
	{
	    //if the entity default is set to 
	    //true then, set the previous default entity to false
	    if ( $this->_mixer->isDefault === true )
	    {
		    $this->getRepository()->update(array('isDefault'=>false), $this->getScope());
	    }
	}
	
	/**
	 * Reorder After Update
	 *
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _beforeEntityUpdate(KCommandContext $context)
	{
	    //if default has changed
	    if ( $this->_mixer->modifications()->isDefault )
	    {
	        $is_default = $this->_mixer->isDefault === true;
	        //if it's true, then reset all existing to false
	        if ( $is_default ) 
	        {
	            $this->getRepository()->update(array('isDefault'=>false), $this->getScope());
	        }
	        else 
	        {
	             $query = $this->getRepository()->getQuery()->id($this->id,'<>')->limit(1);
	             $this->getRepository()->update(array('isDefault'=>true), $query);
	        }
	    }
	}
	
	/**
     * Return a scope of within which to set a default value
     * 
     * @return array
	 */
	public function getScope()
	{
	    return $this->_scope;
	}
}