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
class ComAnahitaSchemaMigration2 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec('delete from jos_components where `option` IN ("com_installer","com_bazaar","com_tagmeta")');
        dbexec('delete from jos_components where `option` IS NULL OR `option` = ""');
        dbexec('update jos_components set admin_menu_link ="" where `option` IN ("com_search","com_todos","com_pages","com_html","com_invites")');
        ///remove privacy_read_mode. some installation may still have it
        try {
            dbexec('alter table jos_anahita_nodes drop column `privacy_read_mode`');
        } catch(Exception $e) {}
        
        $query = 	'DROP TABLE IF EXISTS `jos_core_log_items`, `jos_core_log_searches`, '.
        			'`jos_stats_agents`, `jos_tagmeta`, `jos_migration_backlinks`, `jos_migrations`, '.
        			'';
        
        dbexec($query);
        
        dbexec("DELETE FROM `jos_modules_menu` WHERE `moduleid` IN (SELECT `id` FROM `jos_modules` WHERE `module` IN ('mod_bazaar','mod_footer','mod_login','mod_rokquicklinks'))");
        dbexec("DELETE FROM `jos_modules` WHERE `module` IN ('mod_bazaar','mod_footer','mod_login','mod_rokquicklinks')");
        
        $people = dbfetch('select id,person_username AS username,person_userid AS userid from jos_anahita_nodes where type like "ComActorsDomainEntityActor,ComPeopleDomainEntityPerson,com:people.domain.entity.person" and person_username NOT REGEXP "^[A-Za-z0-9][A-Za-z0-9_-]*$"');        
        foreach($people as $person) 
        {    
            $username = $person['username'];
            $clean    = $username = preg_replace('/(\s|\.|(@\w+))+/','',$username);
            //add a randome number until the username becomes unique
            while ( dbexists("select id from jos_users where username like '$username'") ) {
                $username = $clean.rand(0, 100);
            }
            dbexec("update jos_anahita_nodes set person_username = '$username' where id = {$person['id']}");            
            dbexec("update jos_users set username = '$username' where id = {$person['userid']}");
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