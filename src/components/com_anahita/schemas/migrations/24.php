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
class ComAnahitaSchemaMigration24 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec("UPDATE `#__nodes` SET type = CONCAT('ComTagsDomainEntityNode,', type) WHERE type LIKE 'ComHashtagsDomainEntityHashtag%' OR type LIKE 'ComLocationsDomainEntityLocation%'");
    }

   /**
    * Called when rolling back a migration
    */
    public function down()
    {
        //add your migration here
    }
}