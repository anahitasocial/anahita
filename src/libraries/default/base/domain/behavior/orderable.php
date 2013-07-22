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
 * Orderable Behavior 
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDomainBehaviorOrderable extends AnDomainBehaviorAbstract
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
				'ordering'=>array('default'=>0)
			),
			'aliases' => array(
				'order' => 'ordering'
			)
		));
		
		parent::_initialize($config);
	}
	
	/**
	 * Before Update
	 * 
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _beforeEntityUpdate(KCommandContext $context)
	{
		if ( $this->modifications()->ordering ) {
			
			$store    = $this->getRepository()->getStore();
			$query	  = $this->getRepository()->getQuery();
			$change   = $this->modifications()->ordering;
		
			if( $change->new - $change->old < 0 ) 
			{
				$query->update('@col(ordering) = @col(ordering) + 1');
				$query->where('ordering', '>=',  $change->new)
					  ->where('ordering', '<',   $change->old);
			} 
			else 
			{
				$query->update('@col(ordering) = @col(ordering) - 1');
				$query->where('ordering', '>',   $change->old)
					  ->where('ordering', '<=',  $change->new);
			}
						
			$store->execute($query);
		}		
	}
	
	/**
	 * Reorders all the entities
	 * 
	 * @return void
	 */
	public function reorder()
	{
		$store    = $this->getRepository()->getStore();
		$query 	  = $this->getRepository()->getQuery();
		$store->execute('SET @order = 0');
		$query->update('@col(ordering) = (@order := @order + 1)')->order('ordering', 'ASC');
		$store->execute($query);
	}
	
	/**
	 * Set the order before inserting
	 * 
	 * @param  KCommandContext $context
	 * @return void
	 */
	protected function _beforeEntityInsert(KCommandContext $context)
	{
		$max = $this->getRepository()->getQuery()->fetchValue('MAX(@col(ordering))');
		$this->ordering = $max + 1;
	}
	
	/**
	 * Reorder After Update
	 * 
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _afterEntityUpdate(KCommandContext $context)
	{
		if ( $this->modifications()->ordering )
			$this->reorder();
	}
	
	/**
	 * Reorder After Delete
	 * 
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _afterEntityDelete(KCommandContext $context)
	{
		$this->reorder();
	}	
}

?>