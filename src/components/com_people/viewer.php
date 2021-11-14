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
 * @link       http://www.Anahita.io
 */
class ComPeopleViewer extends AnObject implements AnServiceInstantiatable
{
    /**
      * Force creation of a singleton.
      *
      * @param AnConfigInterface  $config    An optional AnConfig object with configuration options
      * @param AnServiceInterface $container A AnServiceInterface object
      *
      * @return AnServiceInstantiatable
      */
     public static function getInstance(AnConfigInterface $config, AnServiceInterface $container)
     {
         if (! $container->has($config->service_identifier)) {
             $person = null;

             if (PHP_SAPI != 'cli') {
                $session = AnService::get('com:sessions');
                $person = $session->has('person') ? $session->get('person') : null;
             }

             $repository = AnService::get('repos:people.person');

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
