<?php

/**
 * Default Entity Serializer.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsDomainSerializerDefault extends AnDomainSerializerDefault
{
    public function toSerializableArray($entity)
    {
        $data = new KConfig();
        $data[$entity->getIdentityProperty()] = $entity->getIdentityId();

        $data['name'] = $entity->name;

        if ($entity->isOrderable()) {
            $data['ordering'] = $entity->ordering;
        }

        return KConfig::unbox($data);
    }
}
