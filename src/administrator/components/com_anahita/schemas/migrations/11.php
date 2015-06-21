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
class ComAnahitaSchemaMigration11 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec('DROP TABLE #__opensocial_profiles');    
        dbexec('DELETE FROM #__components WHERE `option` = \'com_cache\' OR `option` = \'com_opensocial\' ');
        dbexec('DELETE FROM #__components WHERE name = "" ');
        dbexec('UPDATE #__components SET `admin_menu_link` = \'option=com_mailer\' WHERE `option` = \'com_mailer\' ');
        
        dbexec('DELETE FROM #__plugins WHERE `element` IN ( \'ptag\', \'syntax\', \'opensocial\', \'mtupgrade\', \'usertype\' ) ');
        
        dbexec('ALTER TABLE jos_anahita_nodes ADD `pinned` TINYINT(1) NOT NULL DEFAULT \'0\' AFTER `enabled`');
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}