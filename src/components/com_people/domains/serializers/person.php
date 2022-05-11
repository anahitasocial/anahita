<?php

/**
 * Person entity serializer.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComPeopleDomainSerializerPerson extends ComActorsDomainSerializerActor
{
    /**
     * {@inheritdoc}
     */
    public function toSerializableArray($entity)
    {
        $data = parent::toSerializableArray($entity);

        $data['username'] = $entity->username;
        $data['givenName'] = $entity->givenName;
        $data['familyName'] = $entity->familyName;
        
        $viewer = $this->getService('com:people.viewer');
        
        if ($viewer->eql($entity) || $viewer->admin()) {
            $data['email'] = $entity->email;
            $data['usertype'] = $entity->usertype;
            $data['gender'] = $entity->gender;
        }

        return $data;
    }
}
