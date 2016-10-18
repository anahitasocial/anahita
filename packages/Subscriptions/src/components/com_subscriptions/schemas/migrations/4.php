<?php

/**
 * LICENSE: ##LICENSE##
 *
 * @package    Com_Subscriptions
 * @subpackage Schema_Migration
 */

/**
 * Schema Migration
 *
 * @package    Com_Subscriptions
 * @subpackage Schema_Migration
 */
class ComSubscriptionsSchemaMigration4 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec('ALTER TABLE `#__subscriptions_transactions` DROP COLUMN `user_id`');
        dbexec('UPDATE `#__subscriptions_transactions` SET `billing_period` = \'Year\' WHERE `billing_period` = \'\' ');
    }

   /**
    * Called when rolling back a migration
    */
    public function down()
    {
        //add your migration here
    }
}
