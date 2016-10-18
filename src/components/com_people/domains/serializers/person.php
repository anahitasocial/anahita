<?php

/**
 * Person entity serializer.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleDomainSerializerPerson extends ComBaseDomainSerializerDefault
{
    /**
     * {@inheritdoc}
     */
    public function toSerializableArray($entity)
    {
        $data = parent::toSerializableArray($entity);

        $data['username'] = $entity->username;

        if (
            KService::has('com:people.viewer') &&
            KService::get('com:people.viewer')->eql($entity) &&
            KService::get('com:people.viewer')->admin()
        ) {
            $data['email'] = $entity->email;
            $data['userType'] = $entity->userType;
        }

        return $data;
    }
}
