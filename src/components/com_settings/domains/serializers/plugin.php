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
 * @link       http://www.Anahita.io
 */
class ComSettingsDomainSerializerPlugin extends ComSettingsDomainSerializerDefault
{
    public function toSerializableArray($entity)
    {
        $data = parent::toSerializableArray($entity);
        
        $data['type'] = $entity->type;
        $data['element'] = $entity->element;
        
        $namespace = $entity->type . '-' . $entity->element;    
        $path = ANPATH_SITE.DS.'plugins'.DS.$entity->type.DS.$entity->element.'.json';

        if ($meta = $this->_getMetaParams($entity, $path, $namespace)) {
            $data['meta'] = $meta;
        }

        return AnConfig::unbox($data);
    }
}