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
 * Revision Controller
 *
 * @category   Anahita
 * @package    Com_pages
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPagesControllerRevision extends ComMediumControllerDefault
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'behaviors' => array('parentable')
        ));
    
        parent::_initialize($config);
        
    }
        
	/**
	 * Restores a page back to a revision
	 * 
	 * @param KCommandContext $context Context paramter
	 * 
	 * @return void
	 */
	protected function _actionRestore($context)
	{
		$revision = $this->getItem();
		$page = $revision->page;
		$page->restore($revision);
		
		$msg = JText::sprintf('COM-PAGES-PAGE-REVISIONS-RESTORATION-CONFIRMATION', $revision->revisionNum);
		$this->setRedirect($page->getURL().'&layout=edit', $msg);
	}
	
	/**
	 * Prevents deletion of a revision
	 * 
	 * @return boolean
	 */
	public function canDelete()
	{
		return false;
	}
}