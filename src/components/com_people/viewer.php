<?php

/**
 * A Factory class to return the current viewer person object.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleViewer extends KObject implements KServiceInstantiatable
{
    /**
      * Force creation of a singleton.
      *
      * @param KConfigInterface  $config    An optional KConfig object with configuration options
      * @param KServiceInterface $container A KServiceInterface object
      *
      * @return KServiceInstantiatable
      */
     public static function getInstance(KConfigInterface $config, KServiceInterface $container)
     {
         if (! $container->has($config->service_identifier)) {

             $person = null;

             if (PHP_SAPI != 'cli') {
                $session = KService::get('com:sessions');
                $person = $session->has('person') ? $session->get('person') : null;
             }

             $repository = KService::get('repos:people.person');

             if(isset($person->id)) {
                 $viewer = $repository->find(array('id' => $person->id));
             } else {

                 $viewer = $repository
                 ->getEntity()
                 ->setData(array(
                    'id' => 0,
                    'usertype' => ComPeopleDomainEntityPerson::USERTYPE_GUEST
                 ))->reset();

                 $viewer->getRepository()->extract($viewer);
             }

             $container->set($config->service_identifier, $viewer);
         }

         return $container->get($config->service_identifier);
     }
}
