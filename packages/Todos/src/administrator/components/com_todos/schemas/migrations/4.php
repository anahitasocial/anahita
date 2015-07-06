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
class ComTodosSchemaMigration4 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec('ALTER TABLE #__todos_todos ENGINE=InnoDB');
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}