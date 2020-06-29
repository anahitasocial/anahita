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
class ComSettingsDomainSerializerPlugin extends ComSettingsDomainSerializerDefault
{
    private $_plugin_config = [];
    
    public function toSerializableArray($entity)
    {
        $data = parent::toSerializableArray($entity);
        
        $data['type'] = $entity->type;
        $data['element'] = $entity->element;

        if ($meta = $this->_getMetaParams($entity)) {
            $data['meta'] = $meta;
        }

        return AnConfig::unbox($data);
    }
    
    private function _getMetaParams($entity)
    {
        $namespace = $entity->type . '-' . $entity->element;    
        $config_file_path = ANPATH_SITE.DS.'plugins'.DS.$entity->type.DS.$entity->element.'.json';

        if(!file_exists($config_file_path)) {
           return;
        }
        
        if (!isset($this->_plugin_config[$namespace])) {
            $this->_plugin_config[$namespace] = json_decode(file_get_contents($config_file_path));
        }
        
        if (!isset($this->_plugin_config[$namespace]->fields)) {
            return;
        }
        
        foreach($this->_plugin_config[$namespace]->fields as $field) {
            $default = isset($field->default) ? $field->default : '';
            $field->value = $entity->getValue($field->name, $default);
        }
        
        return $this->_plugin_config[$namespace]->fields;
    }
}