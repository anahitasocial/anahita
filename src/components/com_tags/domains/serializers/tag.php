<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Tag entity serializer.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComTagsDomainSerializerTag extends ComBaseDomainSerializerDefault
{
    /**
     * {@inheritdoc}
     */
    public function toSerializableArray($entity)
    {
        $data = parent::toSerializableArray($entity);

        $data['creationTime'] = $entity->creationTime->getDate();
        $data['updateTime'] = $entity->updateTime->getDate();
        $data['taggables'] = $entity->taggables->fetch();

        return $data;
    }
}
