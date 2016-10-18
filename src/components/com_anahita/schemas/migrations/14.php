<?php

/**
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
class ComAnahitaSchemaMigration14 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        //add your migration here
        dbexec("ALTER TABLE `#__users` ENGINE=InnoDB");
        dbexec("ALTER TABLE `#__templates_menu` ENGINE=InnoDB");
        dbexec("ALTER TABLE `#__plugins` ENGINE=InnoDB");
        dbexec("ALTER TABLE `#__nodes` ENGINE=InnoDB");
        dbexec("ALTER TABLE `#__components` ENGINE=InnoDB");
    }

   /**
    * Called when rolling back a migration
    */
    public function down()
    {
        //add your migration here
    }
}
