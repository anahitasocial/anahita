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
class ComAnahitaSchemaMigration5 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec('ALTER TABLE #__anahita_nodes DROP COLUMN `tag_count`');
        dbexec('ALTER TABLE #__anahita_nodes CHANGE `tag_ids` `hashtag_ids` TEXT DEFAULT NULL');
        dbexec('ALTER TABLE #__anahita_nodes ADD `hashtagable_count` INT(11) UNSIGNED DEFAULT NULL AFTER `hashtag_ids`');
        dbexec('ALTER TABLE #__anahita_nodes ADD `hashtagable_ids` TEXT DEFAULT NULL AFTER `hashtagable_count`');
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}