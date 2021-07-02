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
class ComBaseDomainSerializerDefault extends AnDomainSerializerDefault
{
    /**
     * {@inheritdoc}
     */
    public function toSerializableArray($entity)
    {
        $data = new AnConfig();
        
        $viewer = $this->getService('com:people.viewer');

        $data[$entity->getIdentityProperty()] = $entity->getIdentityId();

        $data['objectType'] = 'com.'.$entity->getIdentifier()->package.'.'.$entity->getIdentifier()->name;

        if ($entity->isDescribable()) {
            $data['name'] = $entity->name;
            $data['body'] = $entity->body;
            $data['alias'] = $entity->alias;
        }

        if ($entity->inherits('ComBaseDomainEntityComment')) {
            $data['body'] = $entity->body;
        }

        if ($entity->isPortraitable()) {
            $imageURL = array();

            if ($entity->hasPortrait()) {
                $sizes = $entity->getPortraitSizes();
                foreach ($sizes as $name => $size) {
                    $url = $entity->getPortraitURL($name);
                    $parts = explode('x', $size);
                    $width = 0;
                    $height = 0;

                    if (empty($parts)) {
                        continue;
                    } elseif (count($parts) == 1) {
                        $height = $width = $parts[0];
                    } else {
                        $width = $parts[0];
                        $height = $parts[1];
                        //hack to set the ratio based on the original
                        if ($height == 'auto' && isset($sizes['original'])) {
                            $original_size = explode('x', $sizes['original']);
                            $height = ($width * $original_size[1]) / $original_size[0];
                        }
                    }

                    $imageURL[$name] = array(
                        'size' => array('width' => (int) $width,'height' => (int) $height),
                        'url' => $url,
                    );
                }
            }

            $data['imageURL'] = (object) $imageURL;
        }
        
        if ($entity->isCoverable()) {
            $coverURL = array();
            
            if ($entity->hasCover()) {
                $coverSizes = $entity->getCoverSizes();
                foreach ($coverSizes as $name => $size) {
                    $url = $entity->getCoverURL($name);
                    $parts = explode('x', $size);
                    $width = 0;
                    $height = 0;

                    if (empty($parts)) {
                        continue;
                    } elseif (count($parts) == 1) {
                        $height = $width = $parts[0];
                    } else {
                        $width = $parts[0];
                        $height = $parts[1];
                        //hack to set the ratio based on the original
                        if ($height == 'auto' && isset($sizes['original'])) {
                            $original_size = explode('x', $sizes['original']);
                            $height = ($width * $original_size[1]) / $original_size[0];
                        }
                    }

                    $coverURL[$name] = array(
                        'size' => array('width' => (int) $width,'height' => (int) $height),
                        'url' => $url,
                    );
                }
            }
            
            $data['coverURL'] = (object) $coverURL;
        }

        if ($entity->isModifiable()) {
            $data->append(array(
                'author' => null,
                'creationTime' => null,
                'editor' => null,
                'updateTime' => null,
            ));
            
            $data['creationTime'] = $entity->creationTime->getDate();
            $data['updateTime'] = $entity->updateTime->getDate();
            
            if (!is_person($entity)) {
                if (isset($entity->author)) {
                    $author = $entity->author->toSerializableArray();
                    $data['author'] = array(
                        'id' => $author['id'],
                        'objectType' => $author['objectType'],
                        'name' => $author['name'],
                        'alias' => $author['alias'],
                        'givenName' => $author['givenName'],
                        'familyName' => $author['familyName'],
                        'username' => $author['username'],
                        'imageURL' => $author['imageURL'],
                    );
                }
                
                if (isset($entity->editor)) {
                    $editor = $entity->author->toSerializableArray();
                    $data['editor'] = array(
                        'id' => $editor['id'],
                        'objectType' => $editor['objectType'],
                        'name' => $editor['name'],
                        'alias' => $editor['alias'],
                        'givenName' => $editor['givenName'],
                        'familyName' => $editor['familyName'],
                        'username' => $editor['username'],
                        'imageURL' => $editor['imageURL'],
                    );
                }
            }
        }

        if ($entity->isCommentable()) {
            $data['openToComment'] = (bool) $entity->openToComment;
            $data['numOfComments'] = $entity->numOfComments;
            $data['lastCommentTime'] = $entity->lastCommentTime ? $entity->lastCommentTime->getDate() : null;
            $data['lastComment'] = null;
            $data['lastCommenter'] = null;

            if (isset($entity->lastComment)) {
                $data['lastComment'] = $entity->lastComment->toSerializableArray();
            }

            if (isset($entity->lastCommenter)) {
                $data['lastCommenter'] = $entity->lastCommenter->toSerializableArray();
            }
        }

        if ($entity->isSubscribable()) {
            $data['subscriberCount'] = $entity->subscriberCount;
            $data['isSubscribed'] = $entity->subscribed($viewer);
        }

        if ($entity->isVotable()) {
            $data['voteUpCount'] = $entity->voteUpCount;
            $data['isVotedUp'] = $entity->votedUp($viewer);
        }
        
        if ($entity->isParentable()) {
            $data['parentId'] = $entity->parent->id;
        }

        if (!is_person($entity) && $entity->isOwnable()) {
            $owner = $entity->owner->toSerializableArray();
            $data['owner'] = array(
                'id' => $owner['id'],
                'objectType' => $owner['objectType'],
                'name' => $owner['name'],
                'alias' => $owner['alias'],
                'imageURL' => $owner['imageURL'],
            );
        }
        
        if ($entity->isGeolocatable()) {
            $data['longitude'] = $entity->geoLongitude;
            $data['latitude'] = $entity->geoLatitude;
        }

        if ($entity->inherits('ComLocationsDomainEntityLocation')) {
            $data['longitude'] = $entity->longitude;
            $data['latitude'] = $entity->latitude;
            $data['address'] = (string) $entity->address;
            $data['city'] = (string) $entity->city;
            $data['state_province'] = (string) $entity->state_province;
            $data['country'] = (string) $entity->country;
            $data['postalcode'] = (string) $entity->postalcode; 
        }
        
        if ($entity->isAuthorizer()) {
            $data['authorized'] = array(
                'administration' => $entity->authorize('administration') == true,
                'edit' => $entity->authorize('edit'),
                'delete' => $entity->authorize('delete'),
            );
        }
        
        if ($data['authorized']['edit'] && $entity->isPrivatable()) {
            $data['access'] = $entity->access;
        }

        return AnConfig::unbox($data);
    }
}
