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
class ComAnahitaSchemaMigration18 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        if(!dbexists('SHOW COLUMNS FROM `#__nodes` LIKE "verified"')) {
          dbexec("ALTER TABLE `#__nodes` ADD `verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `enabled`");
        }
    }

   /**
    * Called when rolling back a migration
    */
    public function down()
    {
        //add your migration here
    }
}
