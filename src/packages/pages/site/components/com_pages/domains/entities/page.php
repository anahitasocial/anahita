<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Pages
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Page Entity
 *
 * @category   Anahita
 * @package    Com_pages
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPagesDomainEntityPage extends ComMediumDomainEntityMedium 
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
			'behaviors' => array(
				'enableable'
			),
			
			'attributes' => array(
				'name'			=> array('required'=>true),
				'excerpt'		=> array('required'=>true,'format'=>'string')
			),
			
			'relationships' => array(
				'revisions'		
			),
			'aliases' => array(
				'published' => 'enabled',
				'title'		=> 'name'
			)
		));
		
        $config->append(array(
            'behaviors' => array(
                'modifiable'  => array(
                    'modifiable_properties' => array('excerpt','name','body')
                ),
                //h2-h6 not allowed in the comments
                'commentable' => array('comment'=>array('format'=>'com://site/medium.filter.post'))
            )        
        ));
        
		parent::_initialize($config);
	}
	
	/**
	 * Creates a new revision before updating
	 * 
	 * @return void
	 */
	protected function _beforeEntityUpdate(KCommandContext $context)
	{		
		$modifications = $this->getModifiedData();
		
		if ( isset($this->__restored) )
			return;
				
		if ( $modifications->name || $modifications->body || $modifications->excerpt ) 
		{
			$revision = $this->addNewRevision();
			
			foreach($modifications as $property => $change) {
				$revision->$property = $change->old;
			}
		}		
	}
	
	/**
	 * Restore a page back to one of it's revision num
	 *
	 * @param  int $revision
	 * @return void
	 */
	public function restore($revision)
	{
		$revision = $this->revisions->find(array('revisionNum'=>$revision->revisionNum));
		
		if ( $revision ) 
		{
			$this->__restored = true;
			$this->setData(array(
				'name' 		=> $revision->title,
				'body' 		=> $revision->description,
				'exceprt'	=> $revision->excerpt
			), AnDomain::ACCESS_PROTECTED);
		}
	}
	
	/**
	 * Creates new revision
	 * 
	 * @return ComPagesDomainEntityRevision
	 */
	public function addNewRevision()
	{	    
		return $this->revisions->addNew(array(
			'component'		=> $this->component,
			'author'		=> get_viewer(),
			'owner'			=> $this->owner,
			'title'			=> $this->title,
			'description'	=> $this->description,
			'excerpt'		=> $this->excerpt,			
			'revisionNum'	=> (int)$this->revisions->fetchValue('MAX(@col(revisionNum))') + 1
		));
	}

//end class	
}