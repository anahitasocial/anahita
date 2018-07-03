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
class ComAnahitaSchemaMigration23 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec("DELETE FROM `#__nodes` WHERE  `type` = 'ComStoriesDomainEntityStory,com:stories.domain.entity.story' AND `name` IN ('avatar_add', 'avatar_edit', 'cover_add', 'cover_edit')");
    }

   /**
    * Called when rolling back a migration
    */
    public function down()
    {
        //add your migration here
    }
}