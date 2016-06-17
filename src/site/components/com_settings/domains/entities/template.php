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
    protected $attributes;

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $this->attributes = array(
            'name' => '',
            'version' => '',
            'creationDate' => '',
            'author' => '',
            'authorEmail' => '',
            'authorUrl' => '',
            'copyright' => '',
            'license' => '',
            'description' => '',
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

            $manifest = json_decode(file_get_contents($path));

            foreach ($this->attributes as $key=>$value) {
                if (array_key_exists($key, $manifest)) {
                    $this->attributes[$key] = $manifest->$key;
                }
            }

            return $this;
        }

        return false;
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
        return $this->attributes[$name];
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
        $this->attributes[$name] = $value;
        
        return $this;
    }
}
