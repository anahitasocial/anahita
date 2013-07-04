<?php

/** 
 * LICENSE: ##LICENSE##
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
        
        $viewer = 
            KService::has('com:people.viewer')? KService::get('com:people.viewer') 
                : null;
                      
        $data[$entity->getIdentityProperty()] = $entity->getIdentityId();
        
        $data['objectType'] = 
            'com.'.$entity->getIdentifier()->package.'.'.$entity->getIdentifier()->name;
        
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
            
            if ( $entity->portraitSet() )
            {
                $sizes    = $entity->getPortraitSizes();
                foreach($sizes as $name => $size) 
                {
                    $url = null;
                    
                    if ( $entity->portraitSet() ) {
                        $url = $entity->getPortraitURL($name);
                    }
                    
                    $parts = explode('x',$size);
                    $width = 0; $height = 0;
                    if ( count($parts) == 0 )
                        continue;
                    
                    elseif ( count($parts) == 1 ) {
                        $height = $width = $parts[0];
                    }
                    else {
                        $width  = $parts[0];
                        $height = $parts[1];  
                        //hack to set the ratio based on the original
                        if ( $height == 'auto' && isset($sizes['original']) ) { 
                           $original_size = explode('x',$sizes['original']);                           
                           $height = ($width * $original_size[1]) / $original_size[0];
                        }
                    }
                    $imageURL[$name] = array(
                        'size' => array('width'=>(int)$width,'height'=>(int)$height),
                        'url'  => $url  
                    );
                }                
            }
            
            $data['imageURL'] = $imageURL;           
        }
        
        if ( $entity->isAdministrable() 
//             && $entity->isAuthorizer() 
//             && $entity->authorize('administration')
            ) 
        {
            $data['administratorIds'] = array_values($entity->administratorIds->toArray());
            if ( $viewer ) {
                $data['isAdministrated'] = $viewer->administrator($entity);   
            }
        }
        
        if ( $viewer && !$viewer->eql($entity) ) {
            if ( $entity->isFollowable() ) {
                $data['isLeader']  = $viewer->following($entity);
            }
            if ( $entity->isLeadable() ) {
                $data['isFollower'] = $viewer->leading($entity);   
            }
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