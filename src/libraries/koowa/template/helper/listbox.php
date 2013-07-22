<?php
/**
 * @version		$Id: listbox.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Template Listbox Helper
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package		Koowa_Template
 * @subpackage	Helper
 */
class KTemplateHelperListbox extends KTemplateHelperSelect
{
	/**
	 * Generates an HTML optionlist based on the distinct data from a model column.
	 *
	 * The column used will be defined by the name -> value => column options in
	 * cascading order.
	 *
	 * If no 'model' name is specified the model identifier will be created using
	 * the helper identifier. The model name will be the pluralised package name.
	 *
	 * If no 'value' option is specified the 'name' option will be used instead.
	 * If no 'text'  option is specified the 'value' option will be used instead.
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 * @see __call()
	 */
    protected function _render($config = array())
 	{
 	    $config = new KConfig($config);
 	    $config->append(array(
 	        'autocomplete' => false
 	    ));

 	    if($config->autocomplete) {
 	        $result = $this->_autocomplete($config);
 	    } else {
 	        $result = $this->_listbox($config);
 	    }

 	    return $result;
 	}

	/**
	 * Generates an HTML optionlist based on the distinct data from a model column.
	 *
	 * The column used will be defined by the name -> value => column options in
	 * cascading order.
	 *
	 * If no 'model' name is specified the model identifier will be created using
	 * the helper identifier. The model name will be the pluralised package name.
	 *
	 * If no 'value' option is specified the 'name' option will be used instead.
	 * If no 'text'  option is specified the 'value' option will be used instead.
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 * @see __call()
	 */
	protected function _listbox($config = array())
 	{
		$config = new KConfig($config);
		$config->append(array(
			'name'		  => '',
			'attribs'	  => array(),
			'model'		  => KInflector::pluralize($this->getIdentifier()->package),
			'deselect'    => true,
		    'prompt'      => '- Select -',
		    'unique'	  => true
		))->append(array(
			'value'		 => $config->name,
			'selected'   => $config->{$config->name},
		    'identifier' => 'com://'.$this->getIdentifier()->application.'/'.$this->getIdentifier()->package.'.model.'.KInflector::pluralize($config->model)
		))->append(array(
			'text'		=> $config->value,
		))->append(array(
		    'filter' 	=> array('sort' => $config->text),
		));

		$list = $this->getService($config->identifier)->set($config->filter)->getList();

		//Get the list of items
 	    $items = $list->getColumn($config->value);
		if($config->unique) {
		    $items = array_unique($items);
		}

		//Compose the options array
        $options   = array();
 		if($config->deselect) {
         	$options[] = $this->option(array('text' => JText::_($config->prompt)));
        }

 		foreach($items as $key => $value)
 		{
 		    $item      = $list->find($key);
 		    $options[] =  $this->option(array('text' => $item->{$config->text}, 'value' => $item->{$config->value}));
		}

		//Add the options to the config object
		$config->options = $options;

		return $this->optionlist($config);
 	}

	/**
	 * Renders a listbox with autocomplete behavior
	 *
	 * @see    KTemplateHelperBehavior::autocomplete
	 * @return string	The html output
	 */
	protected function _autocomplete($config = array())
 	{
		$config = new KConfig($config);
		$config->append(array(
		    'name'		 => '',
			'attribs'	 => array(),
			'model'		 => KInflector::pluralize($this->getIdentifier()->package),
			'validate'   => true,
		))->append(array(
		    'value'		 => $config->name,
		    'selected'   => $config->{$config->name},
			'identifier' => 'com://'.$this->getIdentifier()->application.'/'.$this->getIdentifier()->package.'.model.'.KInflector::pluralize($config->model)
		))->append(array(
			'text'		=> $config->value,
		))->append(array(
		    'filter' 	=> array('sort' => $config->text),
		));

        //For the autocomplete behavior
    	$config->element = $config->value;
    	$config->path    = $config->text;

		$html = $this->getTemplate()->getHelper('behavior')->autocomplete($config);

	    return $html;
 	}

	/**
     * Search the mixin method map and call the method or trigger an error
     *
     * This function check to see if the method exists in the mixing map if not
     * it will call the 'listbox' function. The method name will become the 'name'
     * in the config array.
     *
     * This can be used to auto-magically create select filters based on the
     * function name.
     *
     * @param  string   The function name
     * @param  array    The function arguments
     * @throws BadMethodCallException   If method could not be found
     * @return mixed The result of the function
     */
    public function __call($method, $arguments)
    {
        if(!in_array($method, $this->getMethods()))
        {
            $config = $arguments[0];
            if(!isset($config['name'])) {
                $config['name']  = KInflector::singularize(strtolower($method));
            }

            return $this->_render($config);
        }

        return parent::__call($method, $arguments);
    }
}
