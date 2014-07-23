<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @package    Com_Todos
 * @subpackage Schema_Migration
 */

/**
 * Schema Migration
 *
 * @package    Com_Todos
 * @subpackage Schema_Migration
 */
class ComTodosSchemaMigration2 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        $timeThen = microtime(true);
        
        dbexec('ALTER TABLE #__todos_milestones CHANGE todolists_count todos_count BIGINT(11) NULL DEFAULT NULL');
        dbexec('ALTER TABLE #__todos_milestones ADD COLUMN open_todos_count BIGINT(11) NULL DEFAULT NULL AFTER todos_count');
        
        //now perform the migration and reset all the counters in the process
        
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