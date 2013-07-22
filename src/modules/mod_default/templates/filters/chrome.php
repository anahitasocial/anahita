<?php
/**
* @version      $Id: chrome.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Nooku_Modules
* @subpackage	Default
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Module Chrome Filter
 *
 * @author		Johan Janssens <johan@nooku.org>
* @package      Nooku_Modules
* @subpackage   Default
 */
class ModDefaultTemplateFilterChrome extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
  	/**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct( KConfig $config = null)
    {
        parent::__construct($config);

        include_once JPATH_THEMES.'/system/html/modules.php';

        if(file_exists(JPATH_THEMES.'/'.$config->template.'/html/modules.php')) {
		    include_once JPATH_THEMES.'/'.$config->template.'/html/modules.php';
        }
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
            'priority'   => KCommand::PRIORITY_LOW,
            'template'   => JFactory::getApplication()->getTemplate()
        ));

        parent::_initialize($config);
    }

	/**
	 * Render the module chrome
	 *
	 * @param string Block of text to parse
	 * @return ModDefaultFilterChrome
	 */
	public function write(&$text)
	{
		$data = (object) $this->getTemplate()->getData();

	    foreach($data->styles as $style)
		{
            $method = 'modChrome_'.$style;

			// Apply chrome and render module
		    if (function_exists($method))
			{
		        $data->module->style   = implode(' ', $data->styles);
		        $data->module->content = $text;

				ob_start();
				    $method($data->module, $data->module->params, $data->attribs);
				    $data->module->content = ob_get_contents();
				ob_end_clean();
			}

            $text = $data->module->content;
	    }

	    return $this;
    }
}