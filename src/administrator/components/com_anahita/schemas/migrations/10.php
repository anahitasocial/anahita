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
class ComAnahitaSchemaMigration10 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec('ALTER TABLE #__anahita_nodes
                ADD `cover_filename` VARCHAR(255) NULL AFTER `filesize`,
                ADD `cover_filesize` INT(11) NULL AFTER `cover_filename`,
                ADD `cover_mimetype` VARCHAR(100) NULL AFTER `cover_filesize`');
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}