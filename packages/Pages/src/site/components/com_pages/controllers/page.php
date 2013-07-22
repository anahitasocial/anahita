<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
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
	 * Page post action
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _actionPost($context)
	{		
		$result = parent::_actionPost($context);		
        $this->setRedirect($this->getItem()->getURL().'&layout=edit');        				
		return $result;
	}
}