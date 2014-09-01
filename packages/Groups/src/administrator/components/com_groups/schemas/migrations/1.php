<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @package    Com_Groups
 * @subpackage Schema_Migration
 */

/**
 * Schema Migration
 *
 * @package    Com_Groups
 * @subpackage Schema_Migration
 */
class ComGroupsSchemaMigration1 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec('UPDATE #__anahita_nodes SET `owner_id` = `story_target_id`, `owner_type` = \'com:groups.domain.entity.group\' WHERE `name`=\'actor_follow\' AND `type` = \'ComStoriesDomainEntityStory,com:stories.domain.entity.story\' AND `component` = \'com_groups\' ');
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}