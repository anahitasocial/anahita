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
    * @param path to the configuration file
    */
    protected $config_file_path;

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {

    }

    /**
     * ReLoad the entity properties from storage. Overriding any changes.
     *
     * @param array $properties An array of properties.
     *
     * @return ComSettingsDomainEntitySetting entity object
     */
    public function load($template)
    {
        

        return $this;
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
        return $this;
    }
}
