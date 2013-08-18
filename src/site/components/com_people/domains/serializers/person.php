<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_People
 * @subpackage Domain_Serializer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Person entity serializer
 *
 * @category   Anahita
 * @package    Com_People
 * @subpackage Domain_Serializer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPeopleDomainSerializerPerson extends ComBaseDomainSerializerDefault
{
    /**
     * @{inheritdoc}
     */
    public function toSerializableArray($entity)
    {
        $data = parent::toSerializableArray($entity);
        $data['username'] = $entity->username;
        if ( KService::has('com:people.viewer') && 
             KService::get('com:people.viewer')->eql($entity) ) {            
            $data['email']    = $entity->email;            
        }
        
        return $data;
    }    
}