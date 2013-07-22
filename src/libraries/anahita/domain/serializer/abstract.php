<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Serializer object. This object serializes an entity into an scalar 
 * array of key/value pairs. The result then can be converted into JSON,XML or 
 * any other format.
 * 
 * <strong>This is experimental API and will be changed in future releases</strong> 
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Serializer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainSerializerAbstract extends KObject
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
        $data = array_intersect_key($entity->getData(),
                $entity->description()->getAttributes());
                
        return $data;        
    }    
}