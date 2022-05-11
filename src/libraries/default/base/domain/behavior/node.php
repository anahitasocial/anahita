<?php


 /** 
  * LICENSE: ##LICENSE##.
  * 
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahita.io>
  * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @version    SVN: $Id$
  *
  * @link       http://www.Anahita.io
  */

 /**
  * Node behavior.
  * 
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahita.io>
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.Anahita.io
  */
 class LibBaseDomainBehaviorNode extends AnDomainBehaviorAbstract
 {
     /**
      * An array of repositories.
      * 
      * @var array
      */
     protected $_table_repositories = array();

     /**
      * Constructor.
      *
      * @param AnConfig $config An optional AnConfig object with configuration options.
      */
     public function __construct(AnConfig $config)
     {
         parent::__construct($config);

         if (! $this->_repository->entityInherits('ComBaseDomainEntityNode')) {
             throw new InvalidArgumentException(
                 $this->_repository
                 ->getDescription()
                 ->getEntityIdentifier().' is not a node');
         }
     }

     /**
      * If a repository has more than one resource (table), this mehtod ensures
      * the related records in the other tables.
      * 
      * @param AnCommandContext $context. [node_ids => an array of deleted nodes]
      */
     protected function _afterRepositoryDeletenodes(AnCommandContext $context)
     {
         $ids = $context['node_ids'];

         if ($this->_repository->getResources()->count() > 1) {
             foreach ($this->_repository->getResources() as $resource) {
                 if ($this->_repository->getResources()->main() !== $resource) {
                     $link = $resource->getLink();
                     $child = 'node_id';

                     if (isset($link['child'])) {
                         $child = $link['child'];
                     }

                     $repository = $this->_getRepositoryForTable($resource->getName());
                     $repository->destroy(array('node_id' => $ids));
                 }
             }
         }
     }

     /**
      * Return a repository for a table. This method is used to create
      * a repository on the fly for the para tables.
      * 
      * @param string $table
      * 
      * @return AnDomainRepositoryAbstract
      */
     protected function _getRepositoryForTable($table)
     {
         if (! isset($this->_table_repositories[$table])) {
             $identifier = clone $this->_mixer->getIdentifier();
             $identifier->type = 'repos';
             $identifier->name = uniqid();

             $repository = $this->getService($identifier, array(
                'resources' => array($table),
             ));

             $this->_table_repositories[$table] = $repository;
         }

         return $this->_table_repositories[$table];
     }
 }
