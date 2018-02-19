<?php

/**
 * HTML Tag Element.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseTemplateHelperHtmlElement
{
    /**
     * Attributes.
     *
     * @var array
     */
    public $attributes = array();

    /**
     * Tag Name.
     *
     * @var string
     */
    public $name;

    /**
     * Content of the Tag.
     *
     * @var string
     */
    public $content = '';

    /**
     * Set the content.
     *
     * @param $content string
     *
     * @return object LibBaseTemplateHelperHtmlTag class instance
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the content.
     *
     * @param $content string
     *
     * @return object LibBaseTemplateHelperHtmlTag class instance
     */
    public function addContent($content)
    {
        $this->content .= $content;

        return $this;
    }

    /**
     * Set the tag attribute.
     *
     * <code>
     * $divTag->class = 'some-class'; <div class="some-class"></div>
     * </code>
     *
     * @param $attribute string
     * @param $value string
     */
    public function __set($attribute, $value)
    {
        $this->set($attribute, $value);
    }

    /**
     * Get the tag attribute.
     *
     * @param $attribute string
     *
     * @return string attribute value
     */
    public function __get($attribute)
    {
        if (isset($this->attributes[$attribute])) {
            return $this->attributes[$attribute];
        }
    }

    /**
     * The captured method is used as the attribute of this
     * tag.
     *
     * @param $method
     * @param $args
     *
     * @return object LibBaseTemplateHelperHtmlTag class instance
     */
    public function __call($method, $args)
    {
        $parts = AnInflector::explode($method);
        $name = implode('-', $parts);

        return $this->set($name, $args[0]);
    }

    /**
     * Set an attribute. The parameters can be a key/value or an array of key values.
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
        $args = func_get_args();

        if (count($args) == 2) {
            $this->attributes[$args[0]] = $args[1];
        } elseif (count($args) == 1 && is_array($args[0])) {
            $this->attributes = (array_merge($this->attributes, $args[0]));
        }

        return $this;
    }

    /**
     * Return the tag as a HTML string.
     *
     * @return string
     */
    public function __toString()
    {
        $attributes = array();

        $tag = '<'.$this->name;

        $attr = array();

        foreach ($this->attributes as $key => $value) {
            if (is_array($value)) {
                $value = str_replace('"', "'", json_encode($value));
            }

            $attr[] = $key.'='.'"'.$value.'"';
        }

        $attr = implode(' ', $attr);

        $tag .= ' '.$attr;

        if ((isset($this->content) && !is_null($this->content)) || ($this->name == 'textarea')) {
            $tag .= '>'.$this->content.'</'.$this->name.'>';
        } else {
            $tag .= ' />';
        }

        return $tag;
    }
}
