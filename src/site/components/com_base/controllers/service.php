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
 * @version    SVN: $Id: resource.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Service Controller
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerService extends ComBaseControllerResource
{
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */	
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
        
        //insert the search term query
        $this->_state->insert('q');
	}
		
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
		parent::_initialize($config);
        
		$config->append(array(
		    'behaviors'     => to_hash('serviceable'),
            'toolbars'      => array($this->getIdentifier()->name,'menubar','actorbar'),
            'request'       => array(
                'limit'     => 20,
                'offset'    => 0                
            )
		));
	}
    
	/**
	 * Generic POST action for a medium. If an entity exists then execute edit
	 * else execute add
	 * 
	 * @param KCommandContext $context Context parameter
     * 
	 * @return void
	 */
	protected function _actionPost(KCommandContext $context)
	{
	   	$action = $this->getItem() ? 'edit' : 'add';
		$result = $this->execute($action, $context);
		return $result;
	}	   	
}