<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package	   Com_Todos
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Todolist Entity
 * 
 * @category   Anahita
 * @package	   Com_Todos
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComTodosDomainEntityTodolist extends ComMediumDomainEntityMedium 
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
			'resources'		=> array('todos_todolists'),
			'attributes' 	=> array(
					'name'				=> array('required'=>true),
					'numOfTodos' 		=> array('column'=>'todos_count', 'default'=>'0', 'type'=>'integer', 'write'=>'private'),
					'numOfOpenTodos' 	=> array('column'=>'open_todos_count', 'default'=>'0', 'type'=>'integer', 'write'=>'private')
			),
			'relationships' => array(
				'todos'		
			),
			'behaviors' => array(
				'parentable' => array('parent'=>'milestone')
			)
		));
				
		parent::_initialize($config);
		
		AnHelperArray::unsetValues($config->behaviors, 'commentable');		
	}
	
    /**
     * Set the todolist
     * 
     * @param ComTodosDomainEntityMilestone $parent Milestone  
     *  
     * @return void
     */
    public function setParent($parent)
    {        
        $commands = array('after.insert','after.update','after.delete');
        
        if ( $parent )   
            $this->getRepository()
                ->registerCallback($commands, array($parent,'updateStats'));
        
        if ( $this->parent )
            $this->getRepository()
                ->registerCallback($commands,array($this->parent,'updateStats'));
        
        $this->set('parent', $parent);
    }
	
    /**
     * Update the todolists stats
     * 
     * @return void
     */
    public function updateStats()
    {
        $this->set('numOfTodos', $this->todos->reset()->getTotal());            
        $this->set('numOfOpenTodos', $this->todos->reset()->where('open','=',true)->getTotal());        
    }
}