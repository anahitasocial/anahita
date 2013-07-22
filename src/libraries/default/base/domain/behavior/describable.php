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
 * Decribable Behavior
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDomainBehaviorDescribable extends AnDomainBehaviorAbstract
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
				'name'  => array('format'=>'string'),
				'body',
				'alias'	=> array('format'=>'slug')
			),
			'aliases' => array(
				'title' 		=> 'name',
				'description'	=> 'body' 
			)
		));
		
		parent::_initialize($config);
	}
				
	/**
	 * Override the name setter to set the alias at the same time
	 * 
	 * @param string $name
	 * @return void
	 */
	public function setName($name)
	{
		$this->set('name', $name);
		$this->alias = $name;
	}	
		
	/**
	 * Returns the node URL
	 * 
	 * @return string
	 */
	public function getURL()
	{        
		if ( !isset($this->_mixer->_url) ) 
		{
            $this->_mixer->_url = 'option='.$this->component.'&view='.$this->_mixer->getIdentifier()->name;
            
            if ( $comment = $this->_mixer->getRowData('comment') ) {
                $id = $this->_mixer->getRowData('_id');                
            } 
            else {
                $id = $this->_mixer->id;    
            }
                        
            if ( $id )             
                $this->_mixer->_url .= '&id='.$id;             
            
            if ( $this->alias )
                $this->_mixer->_url .= '&alias='.$this->alias;
                
            if ( $comment ) {
                $this->_mixer->_url .= '&permalink='.$this->_mixer->id;
            }
		}
	
		return $this->_mixer->_url;
	}
	
	/**
	 * If a query keyowrd is set it will incorporate it in the search
	 *
	 * @param  KCommandContext $context
	 * @return void
	 */
	protected function _beforeRepositoryFetch(KCommandContext $context)
	{
		$query = $context->query;
		
		if ( $query->keyword ) 
		{
			$commentable	= $query->getRepository()->hasBehavior('commentable');
			$sub_clause 	= $query->clause();
			$words	    	= (array)KConfig::unbox($query->keyword);
			$words 			= array_unique($words);
                     
            if ( $commentable ) 
            {
                $main_alias   = $query->getRepository()->getResources()->main()->getAlias();
                $comment_type = (string)$query->getRepository()->getDescription()->getProperty('comments')->getChildRepository()->getDescription()->getInheritanceColumnValue();
                $node_type    = (string)$query->getRepository()->getDescription()->getInheritanceColumnValue();                
                $condition    = "(node.parent_id = $main_alias.id AND node.type LIKE '$comment_type') OR (node.id = $main_alias.id)";                
                $query->link(KService::get('repos:base.node'),$condition, array('as'=>'node'));
            }
                
            if ( $context->mode != AnDomain::FETCH_VALUE )
            {
                if ($commentable)
                    $query->select(array(
                        '_id'     => '@col(id)',
                        'id'      => '@col(node.id)',                    
                        'comment' => '@col(id) <> @col(node.id)',                                        
                        'search_result_preview'  => '@col(node.body)'
                    ));
                else $query->select('@col(body) AS search_result_preview');             
            } 
                
			foreach($words as $word) 
            {
				if ( strlen($word) >= 2 ) 
                {
					if ( $commentable ) {
						//$sub_clause->where('node.body','LIKE','%'.$word.'%');
                        $sub_clause->where('@col(node.body) LIKE @quote(%'.$word.'%) OR @col(node.name) LIKE @quote(%'.$word.'%)');                        
					} 
                    else {                        
                        $sub_clause
                             ->where('name','LIKE','%'.$word.'%', 'OR')
                             ->where('body','LIKE','%'.$word.'%', 'OR');                        
                    }
				}
			}
		}
	}
}