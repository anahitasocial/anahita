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
class ComAnahitaSchemaMigration2 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec('delete from jos_components where `option` IN ("com_installer","com_bazaar","com_tagmeta")');
        dbexec('delete from jos_components where `option` IS NULL OR `option` = ""');
        dbexec('update jos_components set admin_menu_link ="" where `option` IN ("com_search","com_todos","com_pages","com_html")');
        //add your migration here
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}