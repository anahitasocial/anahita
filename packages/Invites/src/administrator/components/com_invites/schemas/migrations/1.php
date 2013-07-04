<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @package    Com_Invites
 * @subpackage Schema_Migration
 */


/**
 * Schema Migration
 *
 * @package    Com_Invites
 * @subpackage Schema_Migration
 */
class ComInvitesSchemaMigration1 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec('DROP TABLE IF EXISTS #__invites_invitations');
        
        $query = 'CREATE TABLE IF NOT EXISTS `#__invites_tokens` (
         `id` bigint(11) unsigned NOT NULL auto_increment,
         `inviter_id` bigint(11) unsigned NOT NULL,
         `service` VARCHAR(20),
         `token` VARCHAR(150),
         `used` TINYINT(20) unsigned default 0,
          PRIMARY KEY  (`id`),
          KEY `inviter_id` (`inviter_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=0';
        
        dbexec($query);        
        //add your migration here
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}