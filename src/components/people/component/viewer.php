<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_People
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * A Factory class to return the current viewer person object
 * 
 * @category   Anahita
 * @package    Lib_People
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibPeopleViewer extends KObject implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton
     *
     * @param KConfigInterface 	$config    An optional KConfig object with configuration options
     * @param KServiceInterface	$container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier))
        {            
            $id = JFactory::getUser()->id;
            
            if ( !$id )
            {
                $viewer = $container->get('repos://site/people.person')
                ->getEntity()
                ->setData(array('userType'=>'Guest'), AnDomain::ACCESS_PROTECTED);
                $viewer->set('id',0);
                $viewer->getRepository()->extract($viewer);
            }
            else
            {   
                $viewer = $container->get('repos://site/people.person')->find(array('userId'=>$id));                                
                
                if ( !$viewer ) {
                    $viewer	 = $container->get('com://site/people.helper.person')->createFromUser( JFactory::getUser() );
                    $viewer->save();
                }
            }
            
            $container->set($config->service_identifier, $viewer);
        }
    
        return $container->get($config->service_identifier);
    }
}

?>