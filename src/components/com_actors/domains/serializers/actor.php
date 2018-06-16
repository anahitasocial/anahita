<?php

/**
 * Actor entity serializer.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsDomainSerializerActor extends ComBaseDomainSerializerDefault
{
    /**
     * {@inheritdoc}
     */
    public function toSerializableArray($entity)
    {
        $data = parent::toSerializableArray($entity);
        $viewer = $this->getService('com:people.viewer');
    
        if ($viewer && !$viewer->eql($entity)) {
            if ($entity->isFollowable()) {
                $data['isLeader'] = $viewer->following($entity);
            }

            if ($entity->isLeadable()) {
                $data['isFollower'] = $viewer->leading($entity);
                $data['isBlocked'] = $viewer->blocking($entity);
            }
        }
        
        if ($entity->isFollowable()) {
            $data['followerCount'] = $entity->followerCount;
        }

        if ($entity->isLeadable()) {
            $data['leaderCount'] = $entity->leaderCount;
            $data['mutualCount'] = $entity->mutualCount;
        }
        
        // @todo check for $entity->isAuthorizer() and $entity->authorize('administration') scenarios later on
        if ($entity->isAdministrable()) {
            $data['administratorIds'] = array_values($entity->administratorIds->toArray());

            if ($viewer) {
                $data['isAdministrated'] = $viewer->administrator($entity);
            }
        }
    
        return $data;
    }
}