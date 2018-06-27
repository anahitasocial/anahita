<?php

/**
 * LICENSE: ##LICENSE##
 *
 * @package    Com_Connect
 * @subpackage Schema_Migration
 */

/**
 * Schema Migration
 *
 * @package    Com_Connect
 * @subpackage Schema_Migration
 */
class ComConnectSchemaMigration2 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec('DELETE FROM `#__connect_sessions` WHERE owner_type != "com:people.domain.entity.person"');
    }

   /**
    * Called when rolling back a migration
    */
    public function down()
    {
        //add your migration here
    }
}