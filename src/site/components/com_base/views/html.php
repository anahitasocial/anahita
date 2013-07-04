<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * HTML View Class
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseViewHtml extends LibBaseViewHtml
{
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
						
		if ( $config->list_item_view )
		{
		    //add a template alias
		    $this->getTemplate()->getFilter('alias')->append(array(
		            '@listItemView()'=>'$this->getHelper(\'controller\')->getView(\''.$config->list_item_view.'\')'
		    ));
		}
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
       	$paths[] = dirname($this->getIdentifier()->filepath).'/html';
       	$paths[] = implode(DS, array(JPATH_THEMES, JFactory::getApplication()->getTemplate(), 'html', $this->getIdentifier()->type.'_'.$this->getIdentifier()->package, $this->getName()));
       	
		$config->append(array(
		    'template_filters' => array('module'),
			'template_paths'   => $paths			
		));
				
		parent::_initialize($config);
	}
}