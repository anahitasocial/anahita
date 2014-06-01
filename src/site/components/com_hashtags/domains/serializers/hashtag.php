<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Hashtags
 * @subpackage Domain_Serializer
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Hashtag entity serializer
 *
 * @category   Anahita
 * @package    Com_Hashtags
 * @subpackage Domain_Serializer
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComHashtagsDomainSerializerHashtag extends ComBaseDomainSerializerDefault
{
    /**
     * @{inheritdoc}
     */
    public function toSerializableArray($entity)
    {
        $data = parent::toSerializableArray($entity);
        
        $data['hashtagableCount'] = $entity->hashtagableCount;
        $data['creationTime'] = $entity->creationTime->getDate();
        $data['updateTime'] = $entity->updateTime->getDate();
        
        unset($data['author']);
        unset($data['editor']);
        
        return $data;
    }    
}