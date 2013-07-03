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
 * HTML Helper. Can be used in the views to generate HTML elements 
 * 
 * <code>
 *  //creates a link tag
 *  <?= @html('a','someurl')->class('link-class') ?>
 *  
 *  //creates a select box and set the selected value to 1
 *  <?= @html('select', 'select-name', array('options'=>array('value1','value2'),1));
 * 
 * </code>
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseTemplateHelperHtml extends KTemplateHelperAbstract implements KServiceInstantiatable
{ 
    /**
     * Force creation of a singleton
     *
     * @param KConfigInterface 	$config    An optional KConfig object with configuration options
     * @param KServiceInterface	$container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier))
        {
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }
    
        return $container->get($config->service_identifier);
    }
        
	/**
	 * Return a tag object. This method clones a prototype tag instead of instantiating a new tag to optimize
	 * memory and speed consumption
	 * 
	 * @param $name string
	 * @param $content string
	 * @param $attributes array
     * 
	 * @return LibBaseTemplateHelperHtmlElement
	 */
	public function tag($name, $content, $attributes=array())
	{
		static $instance;
		
		$instance 		  	  = $instance ? clone $instance : new LibBaseTemplateHelperHtmlElement();
		$instance->name 	  = $name;
		$instance->content    = $content;
		$instance->attributes = $attributes;
		return $instance;
	}

	/**
	 * Create select option tags. The options are passed as an associative array of $value, $content
	 * $value being the option tag value and the $content, the value of the option 
	 * 
	 * @param  $options array select options
	 * @param  $selected string[Optional] the value selected
     * 
	 * @return LibBaseTemplateHelperHtmlElement
	 */
	public function options($options, $selected = array()) 
	{
		$options     = (array)KConfig::unbox($options);
		$selected    = (array)KConfig::unbox($selected);
		$tags		 = array();	
		
		foreach($options as $value => $content) 
		{
				if ( is_array($content) && count($content) == 2 ) {
					$value   = $content[0];
					$content = $content[1];
				}
				
				if ( in_array($value, $selected) )
					$tag = '<option selected value="'.$value.'">'.$content.'</option>';
				else
					$tag = '<option value="'.$value.'">'.$content.'</option>';
					
				$tags[] = $tag;
		}
		
		return implode("\n",$tags);
	}	
	
	/**
	 * Create a select tag. 
	 * 
	 * @param $name string
	 * @param $selectedOption string|array
	 * @param $attributes array 
     * 
	 * @return LibBaseTemplateHelperHtmlElement
	 */
	public function select($name, $options = null, $attributes=array())
	{		
		$attributes['name'] = $name;
		
		if ( !isset($attributes['id']) )
			$attributes['id'] = str_replace(array('[',']'),array('_',''), $name);
			
		if ( is_array($options) ) {
			$options = array_merge(array('options'=>array(), 'selected'=>null), $options);
			$options = $this->options($options['options'], @$options['selected']);
		}
		return $this->tag('select', (string)$options, $attributes);
	}	
	
	/**
	 * Create a textarea tag
	 * 
	 * @param  string $name 
	 * @param  string $value 
	 * @param  array  $attributes
     *  
	 * @return LibBaseTemplateHelperHtmlElement
	 */
	public function textarea($name, $value = '', $attributes=array())
	{
		return $this->tag('textarea',$value)
					->set(array('name'=>$name,'id'=>$name))
					->set($attributes);
	}	
	
	/**
	 * Create an input field tag
	 * 
	 * @param  string $type
	 * @param  string $name 
	 * @param  string $value 
	 * @param  array  $attributes 
     * 
	 * @return LibBaseTemplateHelperHtmlElement
	 */
	public function input($type, $name, $value = '', $attributes=array())
	{
		
		$id = str_replace(array('[',']'),array('_',''), $name);
		return $this->tag('input',null)
					->set(array('type'=>$type,'value'=>$value,'name'=>$name,'id'=>$id))
					->set($attributes);		
	}	
	
	/**
	 * Create a text field tag
	 * 
	 * @param  string $name 
	 * @param  string $value 
	 * @param  array  $attributes 
     * 
	 * @return LibBaseTemplateHelperHtmlElement
	 */
	public function textfield($name, $value = '', $attributes=array())
	{
		return $this->input('text', $name, $value, $attributes);
	}
	
	/**
	 * Create a hidden field tag
	 * 
	 * @param  string $name 
	 * @param  string $value 
	 * @param  array  $attributes
     *  
	 * @return LibBaseTemplateHelperHtmlElement
	 */
	public function hiddenfield($name, $value = '', $attributes=array())
	{
		return $this->input('hidden', $name, $value, $attributes);
	}		
	
	/**
	 * Create a password field tag
	 * 
	 * @param  string $name 
	 * @param  string $value 
	 * @param  array  $attributes 
     * 
	 * @return LibBaseTemplateHelperHtmlElement
	 */
	public function passwordfield($name, $value = '', $attributes=array())
	{
		return $this->input('password',$name, $value, $attributes);
	}	
	
	/**
	 * Create a button tag
	 * 
	 * @param  string $name 
	 * @param  string $value 
	 * @param  array  $attributes 
     * 
	 * @return LibBaseTemplateHelperHtmlElement
	 */
	public function button($value, $name = null, $attributes=array())
	{
		$name = pick($name, $value);
		return $this->tag('button',$value)
					->set(array('id'=>$name,'name'=>$name))
					->set($attributes);
	}		
	
	/**
	 * Create a link tag
	 * 
	 * @param  string 		$content 
	 * @param  string|array $url 
	 * @param  array  		$attributes 
     * 
	 * @return LibBaseTemplateHelperHtmlElement
	 */
	public function link($content, $url='', $attributes=array())
	{
		$attributes['href'] = $url;
		return $this->tag('a', $content, $attributes);
	}
	
	/**
	 * Create &gt;input type="radio" /&lt;
	 * 
	 * @param  string  $name 
	 * @param  string  $value 
	 * @param  boolean $checked
	 * @param  array  $attributes 
     * 
	 * @return LibBaseTemplateHelperHtmlElement
	 */
	public function radio($name, $value = '', $checked = false, $attributes=array())
	{
		if ($checked) 
			$attributes['checked'] = 'checked' ;
		else	
			unset($attributes['checked']);
			
		return $this->input('radio', $name, $value, $attributes);
	}
	
	/**
	 * Create &gt;input type="checkbox" /&lt;
	 * 
	 * @param  string  $name 
	 * @param  string  $value 
	 * @param  boolean $checked
	 * @param  array  $attributes 
     * 
	 * @return LibBaseTemplateHelperHtmlElement
	 */
	public function checkbox($name, $value = '', $checked = false, $attributes=array())
	{		
		if ($checked) 
			$attributes['checked'] = 'checked' ;
		else	
			unset($attributes['checked']);
			
		return $this->input('checkbox', $name, $value, $attributes);
	}
	
	/**
	 * Create &gt;img src="" /&lt; tag
	 * 
	 * @param  string $src
	 * @param  array $attributes
     * 
	 * @return LibBaseTemplateHelperHtmlElement
	 */
	public function image($src, $attributes=array())
	{
		return $this->tag('img', null, $attributes)->set('src', $src);
	}
	
	/**
	 * Converts methods to tags. For example $this->h1 will create a h1 tag
	 * 	 
	 */
	public function __call($method, $args)
	{
		$inflected = strtolower(KInflector::variablize($method));
		if ( method_exists($this, $inflected) ) {
			return call_user_func_array(array($this, $inflected), $args);
		}
		
		$content 	= isset($args[0]) ? $args[0] : '';
		$attributes = isset($args[1]) ? $args[1] : array();
		return 	$this->tag($method, $content, $attributes);
	}	
}

/**
 * HTML Tag Element
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseTemplateHelperHtmlElement
{
    /**
     * Attributes
     *
     * @var array
     */
    public $attributes = array();

    /**
     * Tag Name
     *
     * @var string
     */
    public $name;

    /**
     * Content of the Tag
     *
     * @var string
     */
    public $content = '';

    /**
     * Set the content
     *
     * @param $content string
     * @return object LibBaseTemplateHelperHtmlTag class instance
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the content
     *
     * @param $content string
     * @return object LibBaseTemplateHelperHtmlTag class instance
     */
    public function addContent($content)
    {
        $this->content .= $content;
        return $this;
    }

    /**
     * Set the tag attribute
     *
     * <code>
     * $divTag->class = 'some-class'; <div class="some-class"></div>
     * </code>
     * @param $attribute string
     * @param $value string
     * @return void
     */
    public function __set($attribute, $value)
    {
        $this->set($attribute, $value);
    }

    /**
     * Get the tag attribute
     *
     * @param $attribute string
     * @return string attribute value
     */
    public function __get($attribute)
    {
        if ( isset($this->attributes[$attribute]) ) return $this->attributes[$attribute];
    }

    /**
     * The captured method is used as the attribute of this
     * tag
     *
     * @param $method
     * @param $args
     * @return object LibBaseTemplateHelperHtmlTag class instance
     */
    public function __call($method, $args)
    {
        $parts = KInflector::explode($method);
        $name  = implode('-', $parts);
        return $this->set($name, $args[0]);
    }

    /**
     * Set an attribute. The parameters can be a key/value or an array of key values
     *
     * <code>
     * $tag->set('id','my-id');
     * $this->set(array('id'=>'my-id'));
     * </code>
     *
     * @return LibBaseTemplateHelperHtmlTag class instances
     */
    public function set()
    {
        $args  = func_get_args();

        if ( count($args) == 2 )
            $this->attributes[$args[0]] = $args[1];
        else if ( count($args) == 1 && is_array($args[0]) ) {
            $this->attributes = ( array_merge($this->attributes, $args[0]) );
        }

        return $this;
    }

    /**
     * Return the tag as a HTML string
     *
     * @return string
     */
    public function __toString()
    {

        $attributes = array();
        $tag  = '<'.$this->name;
        $attr = array();
        foreach($this->attributes as $key=>$value) {
            if ( is_array($value) )
                $value = str_replace('"', "'", json_encode($value));
            $attr[] = $key.'='.'"'.$value.'"';
        }

        $attr = implode(' ',$attr);
        $tag .= ' '.$attr;
        if ( (isset($this->content) && !is_null($this->content)) || ($this->name == 'textarea'))
            $tag .= '>'.$this->content.'</'.$this->name.'>';
        else
            $tag .= ' />';

        return $tag;
    }

}
?>