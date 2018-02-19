<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Default tempalte object is a Kconfig that return the $config->id as the object id.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseTemplateObject extends KConfig implements LibBaseTemplateObjectInterface
{
    /**
     * Instantiate a template object.
     *
     * @param string $name   Template object name
     * @param array  $config Array of configuration
     *
     * @return LibBaseTemplateObject
     */
    public static function getInstance($name, $config = array())
    {
        static $instance;

        if (!$instance) {
            $instance = new static();
        }

        $object = clone $instance;

        $config['name'] = $name;

        $object->append($config)
        ->append(array(
            'attribs' => array(
            'id' => null,
            ),
        ));

        return $object;
    }

    /**
     * Sets the name of the command.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Return a template object that uniquly identifieds a template object.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set a key/value action attribute.
     *
     * @param string $name  Attribute name
     * @param mixed  $value Attribute
     * @param string $glue  If glue is set, then if there's an old value it will be appended
     *
     *  @return LibBaseTemplateObject
     */
    public function setAttribute($name, $value, $glue = false)
    {
        //get attributes
        $attrbs = KConfig::unbox(pick($this->attribs, array()));

        //get the existing value
        $old = isset($attrbs[$name]) ? $attrbs[$name] : null;

        //if glue and existing value then stich the old and new using the glue
        if ($glue && $old) {
            $value = $old.(string) $glue.$value;
        }

        //put the attributes back
        $attrbs[$name] = $value;
        $this->attribs = $attrbs;

        return $this;
    }

    /**
     * Retrn the value of an attribute.
     *
     * @param string $name Attribute name
     *
     * @return string
     */
    public function getAttribute($name)
    {
        return $this->attribs->$name;
    }

    /**
     * Return an array of attributes for the command.
     *
     * @return array
     */
    public function getAttributes()
    {
        return KConfig::unbox(pick($this->attribs, array()));
    }

    /**
     * Adds a missed method as $key/$value.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return LibBaseTemplateObject
     */
    public function __call($method, $arguments)
    {
        $attribute = implode('-', AnInflector::explode($method));
        $this->setAttribute($attribute, $arguments[0], isset($arguments[1]) ? $arguments[1] : null);

        return $this;
    }

    /**
     * Retur the attributes in key="value" spaced.
     *
     * (non-PHPdoc)
     *
     * @see KConfig::__toString()
     */
    public function __toString()
    {
        $attributes = array();

        foreach ($this->getAttributes() as $key => $value) {
            $value = is_string($value) ? '"'.addslashes($value).'"' : json_encode($value);
            $attributes[] = $key.'='.$value;
        }

        return implode(' ', $attributes);
    }
}
