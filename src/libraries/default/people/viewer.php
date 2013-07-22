<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
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
                $container->get('repos://site/people.person')->getStore()->getCommandChain()->disable();
                $viewer = $container->get('repos://site/people.person')->getQuery()->disableChain()->userId($id)->fetch();
                $container->get('repos://site/people.person')->getStore()->getCommandChain()->enable();
                
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