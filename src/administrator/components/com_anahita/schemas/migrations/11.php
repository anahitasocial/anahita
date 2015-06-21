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
    
        //add github gist plugin
        dbexec("INSERT INTO `#__plugins` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES (50, 'Content Filter - Medium', 'medium', 'contentfilter', 0, 1, 1, 0, 0, 0, '0000-00-00 00:00:00', '')");
        
        //remove the photo plugin
        dbexec("DELETE FROM #__plugins WHERE `element` = 'photo' ");
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}