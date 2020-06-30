<?php

/**
 * Default Entity Serializer.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2020 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsDomainSerializerDefault extends AnDomainSerializerDefault
{    
    public function toSerializableArray($entity)
    {
        $data = new AnConfig();
        $data[$entity->getIdentityProperty()] = $entity->getIdentityId();

        $data['name'] = $entity->name;
        $data['enabled'] = $entity->enabled;

        if ($entity->isOrderable()) {
            $data['ordering'] = $entity->ordering;
        }

        return AnConfig::unbox($data);
    }
    
    protected function _getMetaParams($entity, $path, $namespace)
    {   
        if(!file_exists($path)) {
           return;
        }
        
        $config = [];
        
        if (!isset($config[$namespace])) {
            $config[$namespace] = json_decode(file_get_contents($path));
        }
        
        if (!isset($config[$namespace]->fields)) {
            return;
        }
        
        foreach($config[$namespace]->fields as $field) {
            $default = isset($field->default) ? $field->default : '';
            $field->value = $entity->getValue($field->name, $default);
        }
        
        return $config[$namespace]->fields;
    }
}
