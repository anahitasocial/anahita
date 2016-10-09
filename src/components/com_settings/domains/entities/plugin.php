<?php

/**
 * Plugin Domain Entity.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsDomainEntityPlugin extends AnDomainEntityDefault
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'resources' => array('plugins'),
            'attributes' => array(
                  'id',
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
                'type' => 'folder',
             ),
            'auto_generate' => true,
        ));

        return parent::_initialize($config);
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
        $config_file_path = ANPATH_SITE.DS.'plugins'.DS.$this->type.DS.$this->element.'.json';

        if(file_exists($config_file_path)) {

            $config = json_decode(file_get_contents($config_file_path));

            if(isset($config->fields)){
                $fields = $config->fields;
                foreach ($fields as $field) {
                    $key = $field->name;
                    if(isset($property['meta'][$key])){
                        $this->setValue($key, $property['meta'][$key]);
                    }
                }
            }
        }

        parent::setData($property, $default);
    }
}
