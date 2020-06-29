<?php

/**
 * App Entity Serializer.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2020 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsDomainSerializerApp extends ComSettingsDomainSerializerDefault
{
    private $_app_config = [];
    
    public function toSerializableArray($entity)
    {
        $data = parent::toSerializableArray($entity);

        $data['package'] = $entity->package;
        
        if ($meta = $this->_getMetaParams($entity)) {
            $data['meta'] = $meta;
        }

        return AnConfig::unbox($data);
    }
    
    private function _getMetaParams($entity)
    {
        $package = $entity->option;    
        $config_file_path = ANPATH_SITE.DS.'components'.DS.$package.DS.'config.json';

        if(!file_exists($config_file_path)) {
           return;
        }
        
        if (!isset($this->_app_config[$package])) {
            $this->_app_config[$package] = json_decode(file_get_contents($config_file_path));
        }
        
        foreach($this->_app_config[$package]->fields as $field) {
            $default = isset($field->default) ? $field->default : '';
            $field->value = $entity->getValue($field->name, $default);
        }
        
        return $this->_app_config[$package]->fields;
    }
}