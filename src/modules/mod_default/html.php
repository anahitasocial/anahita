<?php
/**
 * @version     $Id: html.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Nooku_Modules
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Module View
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Modules
 * @subpackage  Default
 */
class ModDefaultHtml extends KViewHtml
{
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
        	'media_url'        => KRequest::root() . '/media',
        	'template_filters' => array('chrome'),
            'data'			   => array(
                'styles' => array()
            )
        ));

        parent::_initialize($config);
    }

	/**
	 * Get the name
	 *
	 * @return 	string 	The name of the object
	 */
	public function getName()
	{
		return $this->getIdentifier()->package;
	}

    /**
     * Renders and echo's the views output
     *
     * @return ModDefaultHtml
     */
    public function display()
    {
		//Load the language files.
		//Type only exists if the module is loaded through ComExtensionsModelsModules
		if(isset($this->module->type)) {
            JFactory::getLanguage()->load($this->module->type);
		}

        if(empty($this->module->content))
		{
            $this->output = $this->getTemplate()
                ->loadIdentifier($this->_layout, $this->_data)
                ->render();
		}
		else
		{
		     $this->output = $this->getTemplate()
                ->loadString($this->module->content, $this->_data, false)
                ->render();
		}

        return $this->output;
    }

    /**
     * Set a view properties
     *
     * @param   string  The property name.
     * @param   mixed   The property value.
     */
    public function __set($property, $value)
    {
        if($property == 'module')
        {
            if(is_string($value->params)) {
                $value->params = $this->_parseParams($value->params);
            }
        }

        parent::__set($property, $value);
    }

	/**
     * Method to extract key/value pairs out of a string
     *
     * @param   string  String containing the parameters
     * @return  array   Key/Value pairs for the attributes
     */
    protected function _parseParams( $string )
    {
        $params = array();

        if(!version_compare(JVERSION,'1.6.0','ge'))
        {
            $string = trim($string);

            if(!empty($string))
            {
                foreach(explode("\n", $string) as $line)
                {
                    $param = explode("=", $line, 2);
                    $params[$param[0]] = $param[1];
                }
            }
        }
        else $params = json_decode($string, true);

        $params = new KConfig($params);
        return $params;
    }
}