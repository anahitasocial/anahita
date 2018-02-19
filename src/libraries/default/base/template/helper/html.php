<?php
/**
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseTemplateHelperHtml extends LibBaseTemplateHelperAbstract implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton.
     *
     * @param KConfigInterface  $config    An optional KConfig object with configuration options
     * @param KServiceInterface $container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier)) {
            $classname = $config->service_identifier->classname;
            $instance = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    /**
     * Return a tag object. This method clones a prototype tag instead of instantiating a new tag to optimize
     * memory and speed consumption.
     *
     * @param $name string
     * @param $content string
     * @param $attributes array
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function tag($name, $content, $attributes = array())
    {
        static $instance;

        $instance = $instance ? clone $instance : new LibBaseTemplateHelperHtmlElement();
        $instance->name = $name;
        $instance->content = $content;
        $instance->attributes = $attributes;

        return $instance;
    }

    /**
     * Create select option tags. The options are passed as an associative array of $value, $content
     * $value being the option tag value and the $content, the value of the option.
     *
     * @param  $options array select options
     * @param  $selected string[Optional] the value selected
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function options($options, $selected = array())
    {
        $options = (array) KConfig::unbox($options);
        $selected = (array) KConfig::unbox($selected);
        $tags = array();

        foreach ($options as $value => $content) {
            if (is_array($content) && count($content) == 2) {
                $value = $content[0];
                $content = $content[1];
            }

            if ($value === 0) {
                $value = null;
            }

            if (in_array($value, $selected)) {
                $tag = '<option selected value="'.$value.'">'.$content.'</option>';
            } else {
                $tag = '<option value="'.$value.'">'.$content.'</option>';
            }

            $tags[] = $tag;
        }

        return implode("\n", $tags);
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
    public function select($name, $options = null, $attributes = array())
    {
        $attributes['name'] = $name;

        if (!isset($attributes['id'])) {
            $attributes['id'] = str_replace(array('[', ']'), array('_', ''), $name);
        }

        if (is_array($options)) {
            $options = array_merge(array('options' => array(), 'selected' => null), $options);
            $options = $this->options($options['options'], @$options['selected']);
        }

        return $this->tag('select', (string) $options, $attributes);
    }

    /**
     * Create a textarea tag.
     *
     * @param  string                           $name
     * @param  string                           $value
     * @param  array                            $attributes
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function textarea($name, $value = '', $attributes = array())
    {
        return $this->tag('textarea', $value)
                    ->set(array('name' => $name, 'id' => $name))
                    ->set($attributes);
    }

    /**
     * Create an input field tag.
     *
     * @param string $type
     * @param string $name
     * @param string $value
     * @param array  $attributes
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function input($type, $name, $value = '', $attributes = array())
    {
        $id = str_replace(array('[', ']'), array('_', ''), $name);

        return $this->tag('input', null)
                    ->set(array('type' => $type, 'value' => $value, 'name' => $name, 'id' => $id))
                    ->set($attributes);
    }

    /**
     * Create a text field tag.
     *
     * @param string $name
     * @param string $value
     * @param array  $attributes
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function textfield($name, $value = '', $attributes = array())
    {
        return $this->input('text', $name, $value, $attributes);
    }

    /**
     * Create a hidden field tag.
     *
     * @param  string                           $name
     * @param  string                           $value
     * @param  array                            $attributes
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function hiddenfield($name, $value = '', $attributes = array())
    {
        return $this->input('hidden', $name, $value, $attributes);
    }

    /**
     * Create a password field tag.
     *
     * @param string $name
     * @param string $value
     * @param array  $attributes
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function passwordfield($name, $attributes = array())
    {
        return $this->input('password', $name, '', $attributes);
    }

    /**
     * Create a button tag.
     *
     * @param string $name
     * @param string $value
     * @param array  $attributes
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function button($value, $name = null, $attributes = array())
    {
        $name = pick($name, $value);

        return $this->tag('button', $value)
                    ->set(array('id' => $name, 'name' => $name))
                    ->set($attributes);
    }

    /**
     * Create a link tag.
     *
     * @param string       $content
     * @param string|array $url
     * @param array        $attributes
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function link($content, $url = '', $attributes = array())
    {
        $attributes['href'] = $url;

        return $this->tag('a', $content, $attributes);
    }

    /**
     * Create &gt;input type="radio" /&lt;.
     *
     * @param string $name
     * @param string $value
     * @param bool   $checked
     * @param array  $attributes
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function radio($name, $value = '', $checked = false, $attributes = array())
    {
        if ($checked) {
            $attributes['checked'] = 'checked';
        } else {
            unset($attributes['checked']);
        }

        return $this->input('radio', $name, $value, $attributes);
    }

    /**
     * Create &gt;input type="checkbox" /&lt;.
     *
     * @param string $name
     * @param string $value
     * @param bool   $checked
     * @param array  $attributes
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function checkbox($name, $value = '', $checked = false, $attributes = array())
    {
        if ($checked) {
            $attributes['checked'] = 'checked';
        } else {
            unset($attributes['checked']);
        }

        return $this->input('checkbox', $name, $value, $attributes);
    }

    /**
     * Create &gt;img src="" /&lt; tag.
     *
     * @param string $src
     * @param array  $attributes
     *
     * @return LibBaseTemplateHelperHtmlElement
     */
    public function image($src, $attributes = array())
    {
        return $this->tag('img', null, $attributes)->set('src', $src);
    }

    /**
     * Converts methods to tags. For example $this->h1 will create a h1 tag.
     */
    public function __call($method, $args)
    {
        $inflected = strtolower(AnInflector::variablize($method));
        if (method_exists($this, $inflected)) {
            return call_user_func_array(array($this, $inflected), $args);
        }

        $content = isset($args[0]) ? $args[0] : '';
        $attributes = isset($args[1]) ? $args[1] : array();

        return    $this->tag($method, $content, $attributes);
    }
}
