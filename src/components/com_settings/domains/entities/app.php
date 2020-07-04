<?php

/**
 * App Domain Entity.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsDomainEntityApp extends AnDomainEntityDefault
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'resources' => array('components'),
            'attributes' => array(
                'id',
                'enabled' => array(
                    'default' => 1
                ),
                'meta' => array(
                    'type' => 'json',
                    'default' => 'json',
                    'write' => 'private'
                ),
            ),
            'behaviors' => array(
                'orderable',
                'dictionariable',
                'authorizer',
                'locatable'
            ),
            'aliases' => array(
                'package' => 'option',
             ),
            'auto_generate' => true,
        ));

        parent::_initialize($config);
    }

    /**
     * Set the value of a property by checking for custom setter. An array
     * can be passed to set multiple properties.
     *
     * @param string|array $property Property name
     * @param mixd         $value    Property value
     */
    public function setData($property = AnDomain::ACCESS_PUBLIC, $default = null)
    {
        $config_file_path = ANPATH_SITE.DS.'components'.DS.$this->package.DS.'config.json';

        if(file_exists($config_file_path)) {

            $app_config = json_decode(file_get_contents($config_file_path));
            $fields = $app_config->fields;

            foreach ($fields as $field) {
                $key = $field->name;
                if(isset($property['meta'][$key])){
                    $this->setValue($key, $property['meta'][$key]);
                }
            }
        }

        parent::setData($property, $default);
    }
}
