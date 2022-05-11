<?php

/**
 * Serializer object. This object serializes an entity into an scalar 
 * array of key/value pairs. The result then can be converted into JSON,XML or 
 * any other format.
 * 
 * <strong>This is experimental API and will be changed in future releases</strong> 
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class AnDomainSerializerAbstract extends AnObject
{
    /**
     * Return an array of serializable data of the entity in format of associative array.
     * 
     * The default implementation return an array of scalar attributes
     * 
     * @param AnDomainEntityAbstract $entity
     * 
     * @return array
     */
    public function toSerializableArray($entity)
    {
        $data = array_intersect_key($entity->getData(), $entity->getEntityDescription()->getAttributes());
        return $data;
    }
}
