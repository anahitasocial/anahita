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
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Comment Controller
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerComment extends ComBaseControllerService
{
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */	
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->registerCallback(array('after.voteup','after.votedown'), array($this,'getvoters'));
        
        $this->getCommandChain()
            ->enqueue( $this->getService('anahita:command.event'), KCommand::PRIORITY_LOWEST);        
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
		    'behaviors' => array('publisher','parentable','votable'),
			'publish_comment'  => KRequest::format() == 'html'			
		));
	
		parent::_initialize($config);
	}
		
	/**
	 * Creates a comment
	 * 
	 * @param  KCommandContext $context
	 * @return AnDomainEntityAbstract
	 */
	protected function _actionAdd($context)
	{
		$data = $context->data;
		$body = $data->body;
		$this->setItem($this->parent->addComment($body))->getItem();
	}
	
	/**
	 * Delete a Comment belonging to a node
	 * 
	 * @param KCommandContext $context post data
	 * @return boolean
	 */
	protected function _actionDelete($context)
	{		
		$this->getItem()->delete();
	}
	
	/**
	 * Edit a comment
	 * 
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _actionEdit($context)
	{
		$data = $context->data;
		$this->getItem()->body = $data->body;
		return $this->getItem();
	}	
	
	/**
	 * Sets the default view to the comment views
	 * 
	 * @param 	stirng $view
	 * @return	ComCommentsControllerResource
	 */
	public function setView($view)
	{
		parent::setView($view);

		if ( !$this->_view instanceof LibBaseViewAbstract ) 
		{
			$view       = KInflector::isPlural($view) ? 'comments' : 'comment';
            $defaults[] = 'ComBaseView'.ucfirst($view).ucfirst($this->_view->name);
            $defaults[] = 'ComBaseView'.ucfirst($this->_view->name);
			register_default(array('identifier'=>$this->_view, 'default'=>$defaults));
		}
		
		return $this;
	}	
	
	/**
	 * Generic executation authorization
	 * 
     * @param KCommandContext $context The CommandChain Context
     *
     * @return boolean
	 */
	public function canExecute(KCommandContext $context)
	{        
		$parent  = $this->getParent();
		
        $comment = $this->getItem();		
		//either one of them has to exists
		if ( !pick($parent, $comment) )
			return false;		
		
		if ( !$parent->authorize('access') ) {
			return false;	
		}
        
        return $this->__call('canExecute', array($context));
	}
    
	/**
	 * Returns whether a comment can be added
	 *
	 * @return boolean
	 */
	public function canAdd()
	{
	    return $this->parent->authorize('add.comment');
	}

	/**
	 * Returns whether a comment can be added
     * 
	 * @return boolean
	 */
	public function canEdit()
	{	   
	    return $this->getItem()->authorize('edit');
	}
		
	/**
	 * Returns whether a comment can be added
	 *
	 * @return boolean
	 */
	public function canDelete()
	{	    
	    return $this->getItem()->authorize('delete');
	}
}