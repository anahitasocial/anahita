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
 * Comment Entity
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseDomainEntityComment extends ComBaseDomainEntityNode
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
		    'inheritance' => array('abstract'=>$this->getIdentifier()->package == 'base'),
			'attributes'  => array(
				'body'			=> array('required'=>true)
			),
			'behaviors'		=> array(       
			    'parentable' => array('parent'=>'com:base.domain.entity.node'),			                 				
                //'taggable',
				'modifiable',
				'authorizer',
				'locatable' ,
				'votable'
			)
		));					
		
		parent::_initialize($config);
	}
	
	/**
	 * Returns the URL for a comment
	 * 
	 * @return string 
	 */
	public function getURL()
	{
		return $this->parent->getURL().'&cid='.$this->id;
	}
	
    /**
     * Validating Entity
     * 
     * KCommandContext $context Context
     * 
     * @return void
     */
    protected function _onEntityValidate(KCommandContext $context)
    {
        $this->parent->getRepository()->getBehavior('commentable')
            ->sanitizeComments(array($this));
    }
            
	/**
	 * Resets the comment stats
	 * 
	 * KCommandContext $context Context
	 * 
	 * @return void
	 */
	protected function _afterEntityInsert(KCommandContext $context)
	{	    	    
		$this->parent->getRepository()->getBehavior('commentable')
		    ->resetStats(array($this->parent));
				
		$this->parent->execute('after.comment', array('comment'=>$this));
	}
	
	/**
	 * Resets the comment stats
	 * 
	 * KCommandContext $context Context
	 * 
	 * @return void
	 */
	protected function _afterEntityDelete(KCommandContext $context)
	{	    	    
		$this->parent->getRepository()->getBehavior('commentable')
		    ->resetStats(array($this->parent));
	}
}