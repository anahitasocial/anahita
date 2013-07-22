<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
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
        
        //add the anahita:event.command        
        $this->getRepository()->getCommandChain()
            ->enqueue( $this->getService('anahita:command.event'), KCommand::PRIORITY_LOWEST);
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
		    'abstract_identifier' => 'com:base.domain.entity.node', //node is an abstract entity, can not be stored in database
		    'inheritance_column'  => 'type',
			'resources'	    => array(array('alias'=>$this->getIdentifier()->name, 'name'=>'anahita_nodes')),
			'attributes'    => array(
				'id'   		=> array('key'=>true, 'type'=>'integer', 'read'=>'public'),				
				'component'		=> array('required'=>true,'read'=>'public')
			)
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