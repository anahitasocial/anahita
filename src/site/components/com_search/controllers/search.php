<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Search
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Search controller searches the node or searchable entities and display the
 * result.
 * 
 * The search controller searches name and body of the nodes that are searchable (specicifed by app delegate)
 * for the requested keyword. Once the result is returned, it pass the search result
 * to each app to render
 * 
 * 
 * @category   Anahita
 * @package    Com_Search
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSearchControllerSearch extends ComBaseControllerResource
{
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
		$config->append(array(
			'behaviors'		=> array('ownable'),
			'toolbars'      => array($this->getIdentifier()->name,'menubar','actorbar'),				
            'request'       => array(
            	'limit'     => 20,
				'sort'		=> 'relevant',
				'direction' => 'ASC'                
            )            
		));
		
		parent::_initialize($config);
	}
    
    /**
     * Search and return the result 
     * 
     * @param KCommandContext $context Controller command chain context
     * 
     * @return string The result to render
     */
    protected function _actionGet(KCommandContext $context)
    {           
    	$this->setView('searches');
    	
    	$this->getToolbar('menubar')->setTitle('');    		
    	
    	if ( $this->actor) {
        	$this->getToolbar('actorbar')->setTitle($this->actor->name);
        	$this->getService()->set('mod://site/search.owner', $this->actor);
    	}
    	$this->_state->append(array(
			'search_comments' => false
    	));
    	    	
        $this->_state->insert('term')
        	->insert('scope')
        	->insert('search_comments')
        	->insert('search_leaders');
        
    	JFactory::getLanguage()->load('com_actors');

    	$this->keywords = array_filter(explode(' ', $this->term));
    	
    	$this->scopes			= $this->getService('com://site/search.domain.entityset.scope');
		$this->current_scope	= $this->scopes->find($this->scope);
		
    	$query = $this->getService('com://site/search.domain.query.node')
    				->ownerContext($this->actor)
    				->searchTerm($this->term)
    				->searchComments($this->search_comments)
    				->limit($this->limit, $this->start);
    				
    	if($this->sort == 'recent')
    		$query->order('node.created_on','DESC');
    	else 
    		$query->orderByRelevance();

    	$set = $query->toEntitySet();
    	// initialize current scope to the 1st one having result
    	if ( ! $this->current_scope and $set->getScopes()->getTotal()) {
    		foreach ($set->getScopes() as $scope) {
    			if ($scope->result_count > 0) {
    				$this->current_scope = $scope;
    				break;
    			}
    		}
    	}
    	$set->getQuery()->scope($this->current_scope);
    	
    	$this->_state->setList($set);
        
        parent::_actionGet($context);
    }
    
	/**
     * Set the request information
     *
     * @param array An associative array of request information
     * 
     * @return LibBaseControllerAbstract
     */
    public function setRequest(array $request)
    {
    	parent::setRequest($request);
    	
    	if(isset($this->_request->term))
    	{
    		$term = $this->getService('anahita:filter.term')->sanitize($this->_request->term);
    		$this->_request->term = $term;
    		$this->term = $term;
    	}
    	
    	return $this;
    }
}