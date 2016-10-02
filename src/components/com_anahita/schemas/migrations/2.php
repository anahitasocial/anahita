<?php

/**
 * LICENSE: GPLv3
 */

/**
 * Schema Migration.
 */
class ComAnahitaSchemaMigration2 extends ComMigratorMigrationVersion
{
    /**
     * Called when migrating up.
     */
    public function up()
    {
        dbexec('delete from #__components where `option` IN ("com_installer","com_bazaar","com_tagmeta")');
        dbexec('delete from #__components where `option` IS NULL OR `option` = ""');
        dbexec('update #__components set admin_menu_link ="" where `option` IN ("com_search","com_todos","com_articles","com_pages","com_invites")');
        ///remove privacy_read_mode. some installation may still have it
        try {
            dbexec('alter table #__anahita_nodes drop column `privacy_read_mode`');
        } catch (Exception $e) {
        }

        dbexec('DROP TABLE IF EXISTS `#__core_log_items`');
        dbexec('DROP TABLE IF EXISTS `#__core_log_searches`');
        dbexec('DROP TABLE IF EXISTS `#__stats_agents`');
        dbexec('DROP TABLE IF EXISTS `#__tagmeta`');
        dbexec('DROP TABLE IF EXISTS `#__migration_backlinks`');
        dbexec('DROP TABLE IF EXISTS `#__migrations`');

        dbexec("DELETE FROM `#__modules_menu` WHERE `moduleid` IN (SELECT `id` FROM `#__modules` WHERE `module` IN ('mod_bazaar','mod_footer','mod_login','mod_rokquicklinks'))");
        dbexec("DELETE FROM `#__modules` WHERE `module` IN ('mod_bazaar','mod_footer','mod_login','mod_rokquicklinks')");

        $people = dbfetch('select id,person_username AS username,person_userid AS userid from #__anahita_nodes where type like "ComActorsDomainEntityActor,ComPeopleDomainEntityPerson,com:people.domain.entity.person" and person_username NOT REGEXP "^[A-Za-z0-9][A-Za-z0-9_-]*$"');
        foreach ($people as $person) {
            $username = $person['username'];
            $clean = $username = preg_replace('/(\s|\.|(@\w+))+/', '', $username);
            //add a randome number until the username becomes unique
            while (dbexists("select id from #__users where username like '$username'")) {
                $username = $clean.rand(0, 100);
            }
            dbexec("update #__anahita_nodes set person_username = '$username' where id = {$person['id']}");
            dbexec("update #__users set username = '$username' where id = {$person['userid']}");
        }
    }

    /**
     * Called when rolling back a migration.
     */
    public function down()
    {
        //add your migration here
    }
}
