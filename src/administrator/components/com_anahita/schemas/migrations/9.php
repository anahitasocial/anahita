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
class ComAnahitaSchemaMigration9 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        //looks like these two didn't work in previous migrations    
        dbexec("DROP TABLE #__content_rating");
        dbexec("DELETE FROM #__components WHERE `option` IN  ('com_media', 'com_menus', 'com_modules')");
        
        //add github gist plugin
        dbexec("INSERT INTO `#__plugins` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES (49, 'Content Filter - GithubGist', 'gist', 'contentfilter', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '')");
        
        //remove the syntax plugin
        dbexec("DELETE FROM #__plugins WHERE `element` = 'syntax' ");
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}