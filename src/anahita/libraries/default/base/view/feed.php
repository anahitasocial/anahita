<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 *  Feed View Class
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseViewFeed extends LibBaseViewAbstract
{    	
	/**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
			'mimetype'	  => 'application/rss',
       	));
    	
    	parent::_initialize($config);
    }
    	
	/**
	 * Return the views output
 	 *
	 *  @return string 	The output of the view
	 */
    public function display()
    {   	
    	$doc =& JFactory::getDocument();
    	$entityType = $this->getIdentifier()->path[1];
    	
    	foreach($this->$entityType as $entity)
    	{
    		$item = new JFeedItem();
			$item->title		= $this->_feedItemTitle($entity);
			$item->link			= $this->_feedItemLink($entity);
			$item->description	= $this->_feedItemBody($entity);
			$item->date			= $entity->creationDate;
			$doc->addItem( $item );
    	}
    	
    	return $this;
    }
    
	/**
     * prepares the feed item link
     * 
     * @param medium node object
     * @return string url
     */
	protected function _feedItemTitle($entity)
	{
		return stripslashes($entity->name);
	}
    
	/**
     * prepares the feed item link
     * 
     * @param medium node object
     * @return string url
     */
	protected function _feedItemLink($entity)
	{
		return $this->getRoute($entity->getURL());
	}

    /**
     * prepares the feed item body field
     * 
     * @param medium node object
     * @return string
     */
	protected function _feedItemBody($entity)
	{
		return nl2br(stripslashes($entity->body));
	}
   
//end class    
}