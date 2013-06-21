<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Photos
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Set Photo Edge
 *
 * @category   Anahita
 * @package    Com_Photos
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPhotosDomainEntityEdge extends ComBaseDomainEntityEdge
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
			'aliases' => array(
				'photo' => 'nodeA' ,
				'set' => 'nodeB',
			),
			'attributes' => array(
				'ordering'
			)
		));
		
		parent::_initialize($config);
	}
		
	/**
	 * After adding a relationship, set the photo count for the set;
	 * 
	 * KCommandContext $context Context
	 * 
	 * @return void
	 */
	protected function _afterEntityInsert(KCommandContext $context)
	{	    
		$this->set->setValue('photo_count', $this->set->photos->reset()->getTotal());		
	}

	/**
	 * After deleting a relationship, set the photo count for the set;
	 * 
	 * KCommandContext $context Context
	 * 
	 * @return void
	 */	
	protected function _afterEntityDelete(KCommandContext $context)
	{	    
		$total = $this->set->photos->reset()->getTotal();
		if ( $total > 0 )
			$this->set->setValue('photo_count', $this->set->photos->reset()->getTotal());
		else
			$this->set->delete();
	}
	
//end class	
}