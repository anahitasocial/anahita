<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @package    Com_Topics
 * @subpackage Schema_Migration
 */

/**
 * Schema Migration
 *
 * @package    Com_Topics
 * @subpackage Schema_Migration
 */
class ComTopicsSchemaMigration4 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
       $timeThen = microtime(true); 
       
        //change comment formats from html to string    
        $entities = dbfetch('SELECT id FROM #__anahita_nodes WHERE type LIKE "%com:topics.domain.entity.comment" ');
    
        dboutput("Updating topics' comments. This WILL take a while ...\n");
    
        foreach($entities as $entity)
        {
            $id = $entity['id'];   
            $entity = KService::get('com://site/base.domain.entity.comment')
            ->getRepository()->getQuery()->disableChain()->fetch( $id )->save();  

            //dboutput( $id.', ' );        
        }
        
        dboutput("Topic comments updated!\n");
        
        $timeDiff = microtime(true) - $timeThen;
        dboutput("TIME: ($timeDiff)"."\n");
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}