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
class ComSubscriptionsSchemaMigration3 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        //remove the photo plugin
        dbexec("DELETE FROM #__plugins WHERE `element` = 'access' AND `folder` = 'subscriptions' ");
        
        //remove the dangling transaction records
        dbexec("DELETE FROM #__subscriptions_transactions WHERE user_id NOT IN (SELECT id FROM #__users)");
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}