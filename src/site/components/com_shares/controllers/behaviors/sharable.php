<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Shares
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Publisher Behavior. Publishes stories after an action
 *
 * @category   Anahita
 * @package    Com_Shares
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSharesControllerBehaviorSharable extends KControllerBehaviorAbstract
{
    /**
     * Share an object using share adapters
     * 
     * @param KConfig $config An array options. 
     * mixed object
     * ComActorsDomainEntityActor $subject
     * ComActorsDomainEntityActor $target
     * 
     * @return void
     */
    public function shareObject($config = array())
    {
        $config     = new KConfig($config);        
        
        if  ( !$config->object ) {
            throw new InvalidArgumentException('Not object specified to share');    
        }
        
        $config->append(array(
             'sharer' => get_viewer()   
        ));
        
        if ( $config->object->isOwnable() ) 
        {
            $config->append(array(
                'target' => $config->object->owner
            ));
        }
        
        $request    = new ComSharesSharerRequest($config);        
        $sharers    = new ArrayObject();
        dispatch_plugin('connect.onGetShareAdapters', array('adapters'=>$sharers,'request'=>$request));
        
        if ( $config->sharers ) 
        {
            $sharers = array_filter((array)$sharers, function($sharer) use ($config) {
               return in_array($sharer->getIdentifier()->name, $config['sharers']);
            });           
        }
        
        foreach($sharers as $sharer) {
            $sharer->shareRequest($request);
        }
    }
}