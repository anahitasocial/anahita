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
class ComAnahitaSchemaMigration13 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
       //migrate users usertype    
       dbexec('UPDATE #__users SET `usertype`=\'registered\' WHERE `usertype` NOT LIKE \'%admin%\' ');
       dbexec('UPDATE #__users SET `usertype`=\'administrator\' WHERE `usertype`=\'Administrator\' ');
       dbexec('UPDATE #__users SET `usertype`=\'super-administrator\' WHERE `usertype`=\'Super Administrator\' '); 
       dbexec('ALTER TABLE #__users DROP COLUMN `gid` ');
       dbexec('ALTER TABLE #__users DROP COLUMN `sendEmail` ');
       
       //migrate people usertype
       dbexec('UPDATE #__anahita_nodes SET `person_usertype`=\'registered\' WHERE `person_usertype` NOT LIKE \'%admin%\' ');
       dbexec('UPDATE #__anahita_nodes SET `person_usertype`=\'administrator\' WHERE `person_usertype`=\'Administrator\' ');
       dbexec('UPDATE #__anahita_nodes SET `person_usertype`=\'super-administrator\' WHERE `person_usertype`=\'Super Administrator\' ');
       
       //migrate session table
       dbexec('ALTER TABLE #__session DROP COLUMN `gid` ');
       
       //drop legacy tables
       dbexec('DROP TABLE #__core_acl_aro');
       dbexec('DROP TABLE #__core_acl_aro_groups');
       dbexec('DROP TABLE #__core_acl_aro_map');
       dbexec('DROP TABLE #__core_acl_aro_sections');
       dbexec('DROP TABLE #__core_acl_groups_aro_map');
       dbexec('DROP TABLE #__groups');
       
       //remove anahita from nodes and edges table names
       dbexec('RENAME TABLE #__anahita_nodes TO #__nodes');
       dbexec('RENAME TABLE #__anahita_edges TO #__edges');
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}