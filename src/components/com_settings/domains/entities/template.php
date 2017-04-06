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
            'description' => '...',
            'alias' => null,
            'thumbnail' => null,
            'version' => '0.0.0',
            'authors' => array(),
            'copyright' => '',
            'license' => '',
            'params' => array()
        );
    }

    public function getURL()
    {
        return route('option=com_settings&view=template&alias='.$this->alias);
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
        $path['manifest'] = $this->_buildManifestPath($template);

        if (file_exists($path['manifest'])) {

            $this->alias = $template;
            $this->thumbnail = KRequest::root().'/templates/'.$template.'/thumbnail.png';

            $manifest = json_decode(file_get_contents($path['manifest']));

            foreach ($this->_attributes as $key => $value) {
                if (array_key_exists($key, $manifest)) {
                    $this->$key = $manifest->$key;
                }
            }

            $path['params'] = $this->_buildParamsPath($template);

            if (file_exists($path['params'])) {
                $this->params = parse_ini_file($path['params']);
            }

            return $this;
        }

        return false;
    }

    public function isDefault()
    {
        $settings = $this->getService('com:settings.setting');
        return $settings->template === $this->alias;
    }

    /**
     * Forwards the call to the space commit entities.
     *
     * @return ComSettingsDomainEntitySetting entity object
     */
    public function save()
    {
        $path = $this->_buildParamsPath($this->alias);

        if (file_exists($path)) {
            chmod($path, 0644);
        }

        $params = '';

        foreach ($this->params as $key => $value) {
          $params .= $key.'='.$value."\n";
        }

        try {
          file_put_contents($path, $params);
        } catch (Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }

        chmod($path, 0444);

        return $this;
    }

    public function isDescribable()
    {
        return false;
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
        $meta = $data['meta'];

        foreach ($meta as $key => $value) {
            $this->_attributes['params'][$key] = $value;
        }

        return $this;
    }

    /**
    * Get a param value
    *
    * @param string key
    *
    * @return mixed value
    */
    public function getValue($name)
    {
       return $this->_attributes['params'][$name];
    }

    /**
    * Set a param value
    *
    * @param string key
    * @param mixed value
    *
    * @return null
    */
    public function setValue($name, $value)
    {
       $this->_attributes['params'][$name] = $value;
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

    /**
    *  constructs the path to the template.json file
    *
    * @param string template alias
    *
    * @return string path
    */
    protected function _buildManifestPath($alias)
    {
        return ANPATH_THEMES.DS.$alias.DS.'template.json';
    }

    /**
    *  constructs the path to the params.ini file
    *
    * @param string template alias
    *
    * @return string path
    */
    protected function _buildParamsPath($alias)
    {
        return ANPATH_THEMES.DS.$alias.DS.'params.ini';
    }
}
