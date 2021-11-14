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
class ComSettingsDomainSerializerApp extends ComSettingsDomainSerializerDefault
{
    public function toSerializableArray($entity)
    {
        $data = parent::toSerializableArray($entity);

        $data['package'] = $entity->package;
        
        $path = ANPATH_SITE.DS.'components'.DS.$entity->package.DS.'config.json';
        $namespace = $entity->package;
        
        if ($meta = $this->_getMetaParams($entity, $path, $namespace)) {
            $data['meta'] = $meta;
        }

        return AnConfig::unbox($data);
    }
}