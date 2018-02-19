<?php
/**
 * @category   Anahita
 *
 * @author	   Johan Janssens <johan@nooku.org>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright  Copyright (C) 2018 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class LibBaseTemplateFilterAbstract extends KObject implements LibBaseTemplateFilterInterface
{
    /**
     * The behavior priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * Template object
     *
     * @var object
     */
    protected $_template;

    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config = null)
    {
        parent::__construct($config);
        $this->_priority = $config->priority;
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
            'priority' => KCommand::PRIORITY_NORMAL,
        ));

        parent::_initialize($config);
    }

    /**
     * Get the priority of a behavior
     *
     * @return  integer The command priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Get the template object
     *
     * @return  object	The template object
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Command handler
     *
     * @param   string      The command name
     * @param   object      The command context
     * @return  boolean     Always returns TRUE
     */
    final public function execute($name, KCommandContext $context)
    {
        //Set the template
        $this->_template = $context->caller;

        //Set the data
        $data = $context->data;

        if (($name & LibBaseTemplateFilter::MODE_READ) && $this instanceof LibBaseTemplateFilterRead) {
            $this->read($data);
        }

        if (($name & LibBaseTemplateFilter::MODE_WRITE) && $this instanceof LibBaseTemplateFilterWrite) {
            $this->write($data);
        }

        //Get the data
        $context->data = $data;

        //Reset the template
        $this->_template = null;

        //@TODO : Allows filters to return false and halt the filter chain
        return true;
    }

    /**
     * Method to extract key/value pairs out of a string with xml style attributes
     *
     * @param   string  String containing xml style attributes
     * @return  array   Key/Value pairs for the attributes
     */
    protected function _parseAttributes($string)
    {
        $result = array();

        if (!empty($string)) {
            $attr   = array();

            preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $attr);

            if (is_array($attr)) {
                $numPairs = count($attr[1]);
                for ($i = 0; $i < $numPairs; $i++) {
                    $result[$attr[1][$i]] = $attr[2][$i];
                }
            }
        }

        return $result;
    }
}
