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
class ComTopicsSchemaMigration3 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        $timeThen = microtime(true);    
        
        dboutput("Updating Topics. This may take a while ...\n");
         
        //Use p tags instead of inlines for topics
        $entities = dbfetch('SELECT id, body FROM #__anahita_nodes WHERE type LIKE "%com:topics.domain.entity.topic" ');
        
        foreach($entities as $entity)
        {
            $id = $entity['id'];   
            $topic = KService::get('com://site/topics.domain.entity.topic')
            ->getRepository()->getQuery()->disableChain()->fetch( $id );  
            
            $body = preg_replace('/\n(\s*\n)+/', "</p>\n<p>", $topic->body);
            $topic->body = '<p>'.$body.'</p>';

            $topic->save();
            
            //dboutput( $id.', ' );
        }
        
        dboutput("Topics updated!\n");
         
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