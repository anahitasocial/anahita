<?php

/**
 * Template Domain Entity
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsDomainEntityTemplate extends KObject
{
    /**
    * @param entity atributes
    */
    protected $_attributes;

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $this->_attributes = array(
            'name' => 'Untitled',
            'alias' => null,
            'thumbnail' => null,
            'version' => '0.0.0',
            'creationDate' => '',
            'author' => '',
            'authorEmail' => '',
            'authorUrl' => '',
            'copyright' => '',
            'license' => '',
            'description' => '...',
            'fields' => array()
        );
    }

    /**
     * Load the entity properties from template json file
     *
     * @param string unique identifier
     *
     * @return ComSettingsDomainEntitySetting entity object
     */
    public function load($template)
    {
        $path = JPATH_THEMES.DS.$template.DS.'template.json';

        if (file_exists($path)) {

            $this->alias = $template;
            $this->thumbnail = KRequest::root().'/templates/'.$template.'/thumbnail.png';

            $manifest = json_decode(file_get_contents($path));

            foreach ($this->_attributes as $key => $value) {
                if (array_key_exists($key, $manifest)) {
                    $this->$key = $manifest->$key;
                }
            }

            return $this;
        }

        return false;
    }

    public function isDefault()
    {
        $config = new JConfig();

        return $config->template == $this->alias;
    }

    /**
     * Forwards the call to the space commit entities.
     *
     * @return ComSettingsDomainEntitySetting entity object
     */
    public function save()
    {
        return $this;
    }

    /**
    * method to set an array of data to attributes
    *
    *  @param array of key => value data
    *
    *  @return ComSettingsDomainEntitySetting object
    */
    public function setData(array $data)
    {
        return $this;
    }

    /**
    *  Magic function for getting an attribute
    *
    *  @param string attribute name
    *  @return attribute value
    */
    public function __get($name)
    {
        return $this->_attributes[$name];
    }

    /**
    *  Magic function for setting an attribute
    *
    *  @param string attribute name
    *  @param attribute value
    *
    *  @return ComSettingsDomainEntitySetting object
    */
    public function __set($name, $value)
    {
        $this->_attributes[$name] = $value;

        return $this;
    }
}
