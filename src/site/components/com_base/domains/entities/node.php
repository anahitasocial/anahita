<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * * Basic Anahita Node. A node within the social network represents an object with a distinguished identity. 
 * A person, a photo, a group are good example of a node. Subclasses adds more bevahior to a basic node by extending the node
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseDomainEntityNode extends AnDomainEntityDefault
{
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
		    'inheritance'         => array(
                'abstract'        => $this->getIdentifier()->classname === __CLASS__,
		        'column'          => 'type',
		        'ignore'          => array(),
            ),
			'resources'	    => array(array('alias'=>$this->getIdentifier()->name, 'name'=>'anahita_nodes')),
		    'identity_property' => 'id',
			'attributes'    => array(
				'id'   		=> array('key'=>true, 'type'=>'integer', 'read'=>'public'),				
				'component'		=> array('required'=>true,'read'=>'public')
			),
		   'behaviors' => to_hash(array('node'))
		));
		
		parent::_initialize($config);
	}
	
	/**
	 * Initialize a new node
	 * 
	 * @return void
	 */
	protected function _afterEntityInstantiate(KConfig $config)
	{
		$config->append(array('data'=>array(
			'component'     => 'com_'.$this->getIdentifier()->package
		)));
	}
}