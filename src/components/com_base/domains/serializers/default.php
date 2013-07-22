<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Serializer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Default Entity Serializer
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Serializer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseDomainSerializerDefault extends AnDomainSerializerDefault
{
    /**
     * @{inheritdoc}
     */
    public function toSerializableArray($entity)
    {
        $data = new KConfig();
        
        $data[$entity->getIdentityProperty()] = $entity->getIdentityId();
        
        if ( $entity->isDescribable() ) {
            $data['name']  = $entity->name;
            $data['body']  = $entity->body;
            $data['alias'] = $entity->alias;
        }
        
        if ( $entity->inherits('ComBaseDomainEntityComment') ) {
            $data['body'] = $entity->body;    
        }
        
        if ( $entity->isPortraitable() ) 
        {                                   
            $imageURL = array();            
            $sizes    = array_keys($entity->getPortraitSizes());
            
            if ( empty($sizes) )
                $sizes    = explode(' ','original large medium small thumbnail square');
                            
            foreach($sizes as $size) 
            {
                $url = null;
                
                if ( $entity->portraitSet() ) {
                    $url = $entity->getPortraitURL($size);
                }
                
                $imageURL[$size] = $url;
            } 
            
            $data['imageURL'] = $imageURL;           
        }
        
        if ( $entity->isModifiable() && !is_person($entity) ) 
        {        
            $data->append(array(
                'author'        => null,
                'creationTime'  => null,
                'editor'        => null,
                'updateTime'    => null
            ));
                
            if ( isset($entity->author) ) {
                $data['author']       = $entity->author->toSerializableArray();
                $data['creationTime'] = $entity->creationTime->getDate(); 
            }
            
            if ( isset($entity->editor) ) {
                $data['editor']     = $entity->editor->toSerializableArray();
                $data['updateTime'] = $entity->updateTime->getDate();
            } 
        }
        
        if ( $entity->isCommentable() ) 
        {
            $data['openToComment'] = (bool)$entity->openToComment;
            $data['numOfComments'] = $entity->numOfComments;
            $data['lastCommentTime'] = $entity->lastCommentTime ? $entity->lastCommentTime->getDate() : null;
            $data['lastComment'] = null;
            $data['lastCommenter'] = null;
            
            if ( isset($entity->lastComment) ) {
                $data['lastComment'] = $entity->lastComment->toSerializableArray();                
            }
            
            if ( isset($entity->lastCommenter) ) {
                $data['lastCommenter'] = $entity->lastCommenter->toSerializableArray();                
            }
        }
        
        if ( $entity->isFollowable() ) {
            $data['followerCount'] = $entity->followerCount;   
        }
        
        if ( $entity->isLeadable() ) {
            $data['leaderCount'] = $entity->leaderCount;
            $data['mutualCount'] = $entity->mutualCount;   
        }
        
        if ( $entity->isSubscribable() ) {
            $data['subscriberCount'] = $entity->subscriberCount;
        }        
        
        if ( $entity->isVotable() ) {
            $data['voteUpCount'] = $entity->voteUpCount;
        }
        
        if ( $entity->isOwnable() ) {
            $data['owner'] = $entity->owner->toSerializableArray();
        }
        
        return KConfig::unbox($data);
    }    
}