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
 * Serializer behavior. This behavior allows to serialize an entity into an scalar 
 * array of key/value pairs. The result then can be converted into JSON,XML or 
 * any other format.
 * 
 * NOTE : This is experimental API and will be changed in future releases 
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainBehaviorSerializable extends AnDomainBehaviorAbstract
{
    /**
     * Serializer object
     * 
     * @var AnDomainSerializerAbstract
     */
    protected $_serializer;
    
    /**
     * Return an array of serializable data of the entity in format of associative array
     * 
     * @return array
     */
    public function toSerializableArray()
    {
        return $this->getSerilizer()->toSerializableArray($this->_mixer);      
    }
    
    /**
     * Return a serializer object
     * 
     * @return AnDomainSerializerAbstract
     */
    public function getSerilizer()
    {
        if ( !isset($this->_serializer) )
        {
            $identifier = clone $this->_repository->getIdentifier();
            $identifier->path = array('domain','serializer');
            register_default(array('identifier'=>$identifier,'prefix'=>$this->_repository->getClone()));            
            $this->_serializer = $this->getService($identifier);            
        }
        
        return $this->_serializer;
    }
}