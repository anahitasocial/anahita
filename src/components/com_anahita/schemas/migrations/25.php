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
class ComAnahitaSchemaMigration25 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        //add your migration here
        dbexec('DELETE FROM `#__plugins` WHERE `folder` = "contentfilter"');
        dbexec('DELETE FROM `#__plugins` WHERE `element` IN ("connect", "invites", "subscriptions")');
        dbexec("DELETE FROM `#__components` WHERE `option` IN ('com_subscriptions', 'com_connect', 'com_invites', 'com_dashboard', 'com_pages')");
        dbexec("DELETE FROM `#__nodes` WHERE `type` LIKE '%com:components.domain.entity.assignment' AND `component` IN ('com_subscriptions', 'com_connect', 'com_invites', 'com_dashboard', 'com_pages')");
    
        dbexec('DROP TABLE IF EXISTS `#__invites_tokens`');
        dbexec('DROP TABLE IF EXISTS `#__subscriptions_coupons`');
        dbexec('DROP TABLE IF EXISTS `#__subscriptions_packages`');
        dbexec('DROP TABLE IF EXISTS `#__subscriptions_transactions`');
        dbexec('DROP TABLE IF EXISTS `#__subscriptions_vats`');
        
        dbexec("DELETE FROM `#__nodes` WHERE `component` IN ('com_subscriptions', 'com_connect', 'com_invites', 'com_dashboard', 'com_pages')");
        dbexec("DELETE FROM `#__edges` WHERE `type` LIKE 'ComSubscriptions%' ");
        dbexec("DELETE FROM `#__edges` WHERE `type` LIKE 'ComConnect%' ");
        dbexec("DELETE FROM `#__edges` WHERE `type` LIKE 'ComInvites%' ");
        dbexec("DELETE FROM `#__edges` WHERE `type` LIKE 'ComDashboard%' ");
        dbexec("DELETE FROM `#__edges` WHERE `type` LIKE 'ComPages%' ");
    }

   /**
    * Called when rolling back a migration
    */
    public function down()
    {
        //add your migration here
    }
}