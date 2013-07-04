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
 * Dictionariable Behavior
 * 
 * Dictionariable allows to store key/value pairs for an entity
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDomainBehaviorDictionariable extends AnDomainBehaviorAbstract
{			
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
				'meta' => array('type'=>'json', 'default'=>'json','write'=>'private')	
			)
		));
		
		parent::_initialize($config);
	}
		
	/**
	 * Sets a key value in the object dictionary, If $key is array
     * the it iterates through the array to set the values
	 * 
	 * @param string $key   The dictionary key value
	 * @param string $value The value to be stored
	 * 
	 * @return LibBaseDomainBehaviorDictionariable
	 */
	public function setValue($key, $value = null)
	{
		$meta = clone $this->meta;
        $key  = KConfig::unbox($key);
        if ( is_array($key) ) {
            $data = $key;   
        } else {
            $data = array($key=>$value);   
        }
        
        foreach($data as $key => $value)        
		  $meta[$key] = $value;
		
        $this->set('meta', $meta);
		return $this;
	}
	
	/**
	 * Returns the dictionary value of a given key. If there are no values then return the default 
	 * value
	 * 
	 * @param string $key     The dictionary key value
	 * @param string $default Default value to return if there are no values
	 * 
	 * @return mixed
	 */
	public function getValue($key, $default = null)
	{
		$meta = $this->meta;
		
		if ( isset($meta[$key]) )
			return $meta[$key];
			
		return $default;
	}
}

?>