<?php
/**
 * @version     $Id: chrome.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Chrome Template Filter
 *
 * This filter allows to apply module chrome to a template
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateFilterChrome extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
	/**
     * The module title
     *
     * If set this will be passed to the module chrome rendered. If the renderer support
     * rendering of a title it will be displayed.
     *
     * @var string
     */
    protected $_title;

    /**
     * The module class
     *
     * @var string
     */
    protected $_class;

    /**
     * The module styles
     *
     * @var array
     */
    protected $_styles;

    /**
     * The module attribs
     *
     * @var array
     */
    protected $_attribs;

 	/**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct( KConfig $config = null)
    {
        parent::__construct($config);

        $this->_title   = $config->title;
        $this->_class   = $config->class;
        $this->_styles  = KConfig::unbox($config->styles);
        $this->_attribs = KConfig::unbox($config->attribs);
    }

	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority' => KCommand::PRIORITY_LOW,
        	'title'    => '',
            'class'    => '',
            'styles'   => array(),
            'attribs'  => array(
                'name'  => $this->getIdentifier()->package . '_' . $this->getIdentifier()->name,
            	'first' => null,
                'last'  => null,
            )
        ));

        parent::_initialize($config);
    }

    /**
	 * Apply module chrome to the template output
	 *
	 * @param string Block of text to parse
	 * @return ComDefaultTemplateFilterChrome
	 */
    public function write(&$text)
    {
		$name = $this->getIdentifier()->package . '_' . $this->getIdentifier()->name;

		//Create a module object
		$module   	       = new KObject();
		$module->id        = uniqid();
		$module->module    = 'mod_'.$name;
		$module->content   = $text;
		$module->position  = $name;
		$module->params    = 'moduleclass_sfx='.$this->_class;
		$module->showtitle = (bool) $this->_title;
		$module->title     = $this->_title;
		$module->user      = 0;

		$text = $this->getService('mod://admin/default.html')->module($module)->attribs($this->_attribs)->styles($this->_styles)->display();

        return $this;
    }
}