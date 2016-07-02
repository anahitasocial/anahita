<?php

/**
 * LICENSE: ##LICENSE##
 *
 * @package    Com_Topics
 * @subpackage Schema_Migration
 */

/**
 * Schema Migration
 *
 * @package    Com_Topics
 * @subpackage Schema_Migration
 */
class ComTopicsSchemaMigration5 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
       //some data clean up from legacy dicsussoins app
       dbexec('DELETE FROM `#__edges` WHERE `node_b_type` = \'com:discussions.domain.entity.topic\' ');
       dbexec('DELETE FROM `#__edges` WHERE `node_b_type` = \'com:discussions.domain.entity.board\' ');
    }

   /**
    * Called when rolling back a migration
    */
    public function down()
    {
        //add your migration here
    }
}
