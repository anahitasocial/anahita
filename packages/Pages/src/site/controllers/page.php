<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Pages
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */
 
/**
 * Page Controller
 *
 * @category   Anahita
 * @package    Com_pages
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPagesControllerPage extends ComMediumControllerDefault
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
            'request' => array(
                'order' => 'creationTime'
            ),
            'behaviors' => array(
        		'enablable'
        	) 
        ));   

        parent::_initialize($config);
    }
        
	/**
	 * Browse Pages
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _actionBrowse($context)
	{
		return parent::_actionBrowse($context)->order($this->order, 'DESC');
	}
	
	/**
	 * Redirect after edit
	 * 
	 * (non-PHPdoc)
	 * @see ComBaseControllerService::_actionEdit()
	 */
	protected function _actionEdit(KCommandContext $context)
	{
	    $result = parent::_actionEdit($context);
	    $this->registerCallback('after.edit', array($this, 'redirect'));
	}
	
	/**
	 * Page post action
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	public function redirect(KCommandContext $context)
	{
	    if ( $context->action == 'edit' ||
	         $context->action == 'add'	             
	            )	    
	    {
	        $context->response->setRedirect(JRoute::_($this->getItem()->getURL().'&layout=edit'));
	        
	    } else {
	        return parent::redirect($context);
	    }
	}
}