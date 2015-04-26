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
            
        //change comment formats from html to string    
        $comments = dbfetch('SELECT id FROM #__anahita_nodes WHERE type LIKE "%com:topics.domain.entity.comment" ');
    
        foreach($comments as $comment)
        {
            $id = $comment['id'];   
            $entity = KService::get('com://site/base.domain.entity.comment')
            ->getRepository()->getQuery()->disableChain()->fetch($id)->save();  

            //dboutput( $id.', ' );        
        }
        
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