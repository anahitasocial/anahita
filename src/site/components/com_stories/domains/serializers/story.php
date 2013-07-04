<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Domain_Serializer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Story entity serializer
 *
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Domain_Serializer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesDomainSerializerStory extends ComBaseDomainSerializerDefault
{
    /**
     * @{inheritdoc}
     */
    public function toSerializableArray($entity)
    {        
        $data = array();
        $data['objectType'] = 
            'com.'.$entity->getIdentifier()->package.'.'.$entity->getIdentifier()->name;
        $data['component'] = $entity->component;
        $data['name'] = $entity->name;  
        //$data['subject'] = $entity->subject->toSerializableArray();
        //$data['target']  = $entity->target->toSerializableArray();
        
//        if ( $entity->aggregated() ) 
        {
            $items = $entity->subject;
            if ( is_array($items) )         
            foreach($items as $item) {
                $data['subjects'][] = $item->toSerializableArray();
            } else {
                $data['subject'] = $items->toSerializableArray();
            }

            $items = $entity->target;
            if ( is_array($items) )         
            foreach($items as $item) {
                $data['targets'][] = $item->toSerializableArray();
            } 
            elseif( $items ) {
                $data['target'] = $items->toSerializableArray();
            }
            
            $items = $entity->object;
            if ( is_array($items) )        
            foreach($items as $item) {
                $data['objects'][] = $item->toSerializableArray();
            }
            elseif( $items ) {
                $data['object'] = $items->toSerializableArray();
            }
        }
        
        $data['creatiomTime'] = $entity->creationTime->getDate();        

        if ( $entity->getIdentifier()->name == 'story' ) 
        {
	        foreach($entity->getComments() as $comment) {
	        	$data['comments'][] = $comment->toSerializableArray();
	        }        
        }
        return $data;
    }    
}