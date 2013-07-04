<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Node behavior
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
 class LibBaseDomainBehaviorNode extends AnDomainBehaviorAbstract
 {
     /**
      * An array of repositories
      * 
      * @var array
      */
     protected $_table_repositories = array();
     
     /**
      * Constructor.
      *
      * @param KConfig $config An optional KConfig object with configuration options.
      *
      * @return void
      */
     public function __construct(KConfig $config)
     {
         parent::__construct($config);
         
         if ( !$this->_repository->entityInherits('ComBaseDomainEntityNode') ) {
             throw new InvalidArgumentException($this->_repository
                     ->getDescription()->getEntityIdentifier().' is not a node');
         }
     }

     /**
      * Initializes the default configuration for the object
      *
      * Called from {@link __construct()} as a first step of object instantiation.
      *
      * @param KConfig $config An optional KConfig object with configuration options.
      *
      * @return void
      */
     protected function _initialize(KConfig $config)
     {
         $config->append(array(
     
         ));
     
         parent::_initialize($config);
     }
          
     /**
      * If a repository has more than one resource (table), this mehtod ensures
      * the related records in the other tables
      * 
      * @param KCommandContext $context. [node_ids => an array of deleted nodes]
      * 
      * @return void
      */
     protected function _afterRepositoryDeletenodes(KCommandContext $context)
     {
         $ids = $context['node_ids'];
         
         if ( $this->_repository->getResources()->count() > 1)
         {
             foreach($this->_repository->getResources() as $resource)
             {
                 if ( $this->_repository->getResources()->main() !== $resource )
                 {
                     $link  = $resource->getLink();
                     $child = 'node_id';
                     if ( isset($link['child']) ) {
                         $child = $link['child'];
                     }
                     $repository = $this->_getRepositoryForTable($resource->getName());                    
                     $repository->destroy(array('node_id'=>$ids));
                 }
             }
         }
     }
     
     /**
      * Return a repository for a table. This method is used to create
      * a repository on the fly for the para tables
      * 
      * @param string $table
      * 
      * @return AnDomainRepositoryAbstract
      */
     protected function _getRepositoryForTable($table)
     {
         if ( !isset($this->_table_repositories[$table]) )
         {
             $identifier = clone $this->_mixer->getIdentifier();
             $identifier->type = 'repos';
             $identifier->name = uniqid();
             $repository = $this->getService($identifier, array(
                     'resources' => array($table)
             ));
             $this->_table_repositories[$table] = $repository;
         }
         return $this->_table_repositories[$table];
     }
 }