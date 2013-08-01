<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Custom Elemen
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class JElementCustom extends JElement
{
    /**
     * The template that evaludates the custom code
     * 
     * @var LibBaseTemplateDefault
     */
    protected $_template;
    
    /**
     * Creates a custom form element
     *
     * @param mixed $parent
     * 
     * @return void
     */
    public function __construct($parent = null)
    {
        parent::__construct($parent);
        
        $this->_template = KService::get('com:base.template.default');
        
        $this->_template->addFilter(array('shorttag','alias'));
         
        $this->_template->getFilter('alias')->append(array(
			'@route('	   	=> 'JRoute::_(',
			'@html(\''     	=> '$this->renderHelper(\'com:base.template.helper.html.',
        ));
    }
    
    /**
     * Render a custom element
     *
     * @param string     $name  The name of the element
     * @param string     $value The vlaue of the element
     * @param xmlElement $node  The node xml element
     * @param string     $control_name The control name
     * 
     * @see   JElement
     * 
     * @return string
     */
	public function fetchElement($name, $value, &$node, $control_name)
	{
	    $data = pick(KConfig::unbox($this->_parent->template_data), array());
	    $data['name']          = $name;
	    $data['value']         = $value;
	    $data['node']          = $node;
	    $data['control_name']  = $control_name;	    
	    return (string)$this->_template->loadString($node->data(), $data);	    	
	}
}