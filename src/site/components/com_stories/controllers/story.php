<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Story Controller
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesControllerStory extends ComBaseControllerService
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
        
        $this->_state->insert('name');
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
        $config->append(array(
            'behaviors' => array(
                'serviceable'  => array('except'=>array('edit')),
                'ownable'      => array('default'=>get_viewer())
            )
        ));
        
		parent::_initialize($config);
	}
    
	/**
	 * Creates a new story. This is an internal method and can not be
	 * called from outside. 
	 * 
	 * Check 
	 * ComStoriesControllerPermissionStory::canAdd
	 * 
	 * (non-PHPdoc)
	 * @see ComBaseControllerService::_actionAdd()
	 */
	protected function _actionAdd(KCommandContext $context)
	{
	    $data = $context->data;
        return $this->getRepository()->create($data->toArray());
	}
	
    /**
     * Browse action
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
	protected function _actionBrowse($context)
	{				   
		$query 	  = $this->getRepository()->getQuery()			
					->limit( 20, $this->start );

        
        if ( $this->filter == 'leaders') 
        {
            $ids    = get_viewer()->leaderIds->toArray();
            $ids[]  = get_viewer()->id;
            $query->where('owner.id','IN', $ids);            
        }
		else {
			$query->owner($this->actor);
		}

        $query->aggregateKeys($this->getService('com://site/stories.domain.aggregations'));
        
        $query->order('creationTime','desc');
        
        if ( $this->component ) {
            $query->clause()->component( (array)KConfig::unbox($this->component) );
        }
        
        if ( $this->name ) {
            $query->clause()->name( (array)KConfig::unbox($this->name) );
        }    
        
        if ( $this->subject ) {    
            $query->clause()->where('subject.id','IN', (array)KConfig::unbox($this->subject));   
        }

        return $this->setList($query->toEntitySet())
                    ->getList();
	}
	
	/**
	 * Delete a story
	 * 
	 * @return boolean
	 */
	protected function _actionDelete($context)
	{
	    $context->response->status = KHttpResponse::NO_CONTENT;
        $this->getItem()->delete();
        $context->response->setRedirect(JRoute::_($this->getItem()->owner->getURL()));
	}
}