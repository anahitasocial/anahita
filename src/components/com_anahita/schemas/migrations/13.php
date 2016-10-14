<?php

/**
 * Schema Migration.
 */
class ComAnahitaSchemaMigration13 extends ComMigratorMigrationVersion
{
    /**
     * Called when migrating up.
     */
    public function up()
    {
      //remove anahita from nodes and edges table names
      if (!dbexists('SHOW TABLES LIKE "#__nodes"')) {
          dbexec('RENAME TABLE #__anahita_nodes TO #__nodes');
      }

      if (!dbexists('SHOW TABLES LIKE "#__edges"')) {
          dbexec('RENAME TABLE #__anahita_edges TO #__edges');
      }

        //migrate users usertype
       dbexec('UPDATE `#__users` SET `usertype`=\'registered\' WHERE `usertype` NOT LIKE \'%admin%\' ');
        dbexec('UPDATE `#__users` SET `usertype`=\'administrator\' WHERE `usertype`=\'Administrator\' ');
        dbexec('UPDATE `#__users` SET `usertype`=\'super-administrator\' WHERE `usertype`=\'Super Administrator\' ');
        dbexec('ALTER TABLE `#__users` DROP COLUMN `gid` ');
        dbexec('ALTER TABLE `#__users` DROP COLUMN `sendEmail` ');

       //migrate people usertype
       dbexec('UPDATE `#__nodes` SET `person_usertype`=\'registered\' WHERE `person_usertype` NOT LIKE \'%admin%\' ');
        dbexec('UPDATE `#__nodes` SET `person_usertype`=\'administrator\' WHERE `person_usertype`=\'Administrator\' ');
        dbexec('UPDATE `#__nodes` SET `person_usertype`=\'super-administrator\' WHERE `person_usertype`=\'Super Administrator\' ');

       //migrate session table
       dbexec('ALTER TABLE `#__session` DROP COLUMN `gid` ');

       //drop legacy tables
       dbexec('DROP TABLE #__core_acl_aro');
        dbexec('DROP TABLE #__core_acl_aro_groups');
        dbexec('DROP TABLE #__core_acl_aro_map');
        dbexec('DROP TABLE #__core_acl_aro_sections');
        dbexec('DROP TABLE #__core_acl_groups_aro_map');
        dbexec('DROP TABLE #__groups');

       //legacy component cleanup
       dbexec('DELETE FROM `#__components` WHERE `option` IN (\'com_users\', \'com_user\') ');
        dbexec('UPDATE `#__components` SET `admin_menu_link`=\'option=com_people\', `admin_menu_alt`=\'People\', `admin_menu_img`=\'js/ThemeOffice/component.png\' WHERE `option`=\'com_people\' ');

        dbexec('ALTER TABLE `#__nodes` MODIFY `enabled` tinyint(1) NOT NULL DEFAULT 0');
        dbexec('ALTER TABLE `#__nodes` MODIFY `is_default` tinyint(1) NOT NULL DEFAULT 0');
        dbexec('UPDATE `#__nodes` SET `story_subject_id` = `created_by` WHERE `story_subject_id` = `story_target_id` AND `name` = \'actor_follow\'');
        dbexec('UPDATE `#__nodes` SET `enabled`=1, `access` = \'admins\' WHERE `type` LIKE \'%com:people.domain.entity.person\' AND `enabled` = 0');
    }

    /**
     * Called when rolling back a migration.
     */
    public function down()
    {
        //add your migration here
    }
}
