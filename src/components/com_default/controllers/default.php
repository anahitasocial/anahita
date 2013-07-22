<?php
/**
 * @version     $Id: default.php 2721 2010-10-27 00:58:51Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Controller
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultControllerDefault extends KControllerService
{
	/**
	 * The limit information
	 *
	 * @var	array
	 */
	protected $_limit;

	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_limit = $config->limit;
	}

	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'limit' => array('max' => 100, 'default' => JFactory::getApplication()->getCfg('list_limit'))
        ));

        parent::_initialize($config);
    }

    /**
     * Display action
     *
     * If the controller was not dispatched manually load the langauges files
     *
     * @param   KCommandContext A command context object
     * @return  KDatabaseRow(set)   A row(set) object containing the data to display
     */
    protected function _actionGet(KCommandContext $context)
    {
        //Load the language file for HMVC requests who are not routed through the dispatcher
        if(!$this->isDispatched()) {
            JFactory::getLanguage()->load('com_'.$this->getIdentifier()->package);
        }

        return parent::_actionGet($context);
    }

	/**
     * Browse action
     *
     * Use the application default limit if no limit exists in the model and limit the
     * limit to a maximum.
     *
     * @param   KCommandContext A command context object
     * @return  KDatabaseRow(set)   A row(set) object containing the data to display
     */
    protected function _actionBrowse(KCommandContext $context)
    {
        if($this->isDispatched())
        {
            $limit = $this->getModel()->get('limit');

            //If limit is empty use default
            if(empty($limit)) {
                $limit = $this->_limit->default;
            }

            //Force the maximum limit
            if($limit > $this->_limit->max) {
                $limit = $this->_limit->max;
            }

            $this->limit = $limit;
        }

        return parent::_actionBrowse($context);
    }

	/**
     * Set a request property
     *
     *  This function translates 'limitstart' to 'offset' for compatibility with Joomla
     *
     * @param  	string 	The property name.
     * @param 	mixed 	The property value.
     */
 	public function __set($property, $value)
    {
        if($property == 'limitstart') {
            $property = 'offset';
        }

        parent::__set($property, $value);
  	}
}