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
        dbexec('UPDATE #__anahita_nodes AS node SET node.pinned = ( SELECT tt.sticky FROM #__topics_topics AS tt WHERE tt.node_id = node.id AND tt.sticky = 1 )');
        dbexec('DROP TABLE #__topics_topics');
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}