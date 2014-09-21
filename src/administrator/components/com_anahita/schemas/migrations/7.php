<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @package    Com_Anahita
 * @subpackage Schema_Migration
 */

/**
 * Schema Migration
 *
 * @package    Com_Anahita
 * @subpackage Schema_Migration
 */
class ComAnahitaSchemaMigration7 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec('DROP TABLE IF EXISTS #__menu');
        dbexec('DROP TABLE IF EXISTS #__menu_types');
        dbexec('DELETE FROM #__modules WHERE `module` IN (\'mod_menu\',\'mod_viewer\') AND `client_id` = 0 ');
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}