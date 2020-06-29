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

        if ($entity->isOrderable()) {
            $data['ordering'] = $entity->ordering;
        }

        return AnConfig::unbox($data);
    }
}
