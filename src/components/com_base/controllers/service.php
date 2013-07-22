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
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Service Controller
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerService extends LibBaseControllerService
{
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */	
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
        
        //insert the search term query
        $this->_state->insert('q');
        
        //set viewer in the state
        $this->_state->viewer = $config->viewer;
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
		parent::_initialize($config);
        
		$config->append(array(
            'viewer'        => get_viewer(),
            'language'      => 'com_'.$this->getIdentifier()->package ,
            'toolbars'      => array($this->getIdentifier()->name,'menubar','actorbar'),
            'request'       => array(
                'limit'     => 20,
                'offset'    => 0                
            )            
		));       		
	}
    
    /** 
     * Service Browse
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return AnDomainQuery
     */ 
    protected function _actionBrowse(KCommandContext $context)
    {                
        $context->append(array(
            'query' => $this->getRepository()->getQuery() 
        ));
        
        $query = $context->query;
        
        if ( $this->q ) {
            $query->keyword = explode(' OR ', $this->q);            
        }
        
        if ( $this->hasBehavior('parentable') && $this->getParent() ) {
            $query->parent($this->getParent());
        }
        
        //do some sorting
        if ( $this->sort ) 
        {
            $this->_state->append(array(
                'direction' => 'ac'
            ));            
            
            $dir = $this->direction;
            
            $query->order($this->sort, $dir);
        }
        
        $query->limit( $this->limit , $this->start );
        
        return $this->getState()->setList($query->toEntityset())->getList();
    }
    
	/**
	 * Generic POST action for a medium. If an entity exists then execute edit
	 * else execute add
	 * 
	 * @param KCommandContext $context Context parameter
     * 
	 * @return void
	 */
	protected function _actionPost(KCommandContext $context)
	{                
	   	$action = $this->getItem() && $this->getItem()->persisted() ? 'edit' : 'add';
        
		$result = $this->execute($action, $context);
		
		if ( is($result, 'AnDomainEntityAbstract') && $result->isDescribable() ) {
			$this->setRedirect($result->getURL());
		}
		
		return $result;
	}	
    
    /**
     * Get a toolbar by identifier
     *
     * @return KControllerToolbarAbstract
     */
    public function getToolbar($toolbar, $config = array())
    {
        if ( is_string($toolbar) )
        {
            //if actorbar or menu alawys default to the base
            if ( in_array($toolbar, array('actorbar','menubar','comment')) )
            {
                $identifier       = clone $this->getIdentifier();
                $identifier->path = array('controller','toolbar');
                $identifier->name = $toolbar;               
                register_default(array('identifier'=>$identifier, 'default'=>'ComBaseControllerToolbar'.ucfirst($toolbar)));                
                $toolbar = $identifier;
            }
        }
    
        return parent::getToolbar($toolbar, $config);
    }    	
}