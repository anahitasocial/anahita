<?php

/** 
 * LICENSE: ##LICENSE##
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
 * Restful Controller
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerResource extends LibBaseControllerResource
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

        //load the language
        JFactory::getLanguage()->load( $config->language );
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
            'toolbars'    => array('menubar', $this->getIdentifier()->name),		    
		    'language'    => 'com_'.$this->getIdentifier()->package
		));
				
		parent::_initialize($config);
		
	}	

	/**
	 * Enqueus a message
	 * 
	 * @param string $message
	 * @param string $type
	 */
	public function setMessage($message, $type = 'message')
	{	    
	    $session =& JFactory::getSession();
	    $session->set('application.queue', array(array('message'=>$message, 'type'=>$type)));
	}
	
	/**
	 * Cancel action
	 *
	 * This function will unlock the row(s) and set the redirect to the referrer
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRow	A row object containing the data of the cancelled object
	 */
	protected function _actionCancel(KCommandContext $context)
	{
	    //Create the redirect
	    $context->response->setRedirect(JRoute::_('option=com_'.$this->getIdentifier()->package.'&view='.KInflector::pluralize($this->getIdentifier()->name)));
	}		

	/**
	 * Saves redirecs back to the collection view
	 *
	 * @param KCommandContext $context
	 */
	protected function _actionSave(KCommandContext $context)
	{
	    $this->execute('post', $context);
	    $context->response->setRedirect(JRoute::_('option=com_'.$this->getIdentifier()->package.'&view='.KInflector::pluralize($this->getIdentifier()->name)));
	}	
}