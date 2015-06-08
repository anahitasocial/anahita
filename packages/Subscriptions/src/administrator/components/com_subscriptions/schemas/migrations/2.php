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
class ComSubscriptionsSchemaMigration2 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec('ALTER TABLE #__subscriptions_coupons
        ADD `created_on` datetime DEFAULT NULL AFTER `usage`,
        ADD `created_by` bigint(11) unsigned DEFAULT NULL AFTER `created_on`,
        ADD INDEX `created_by` (`created_by`),
        ADD `modified_on` datetime DEFAULT NULL AFTER `created_by`,
        ADD `modified_by` bigint(11) unsigned DEFAULT NULL AFTER `modified_on`,
        ADD INDEX `modified_by` (`modified_by`)  
        ');
        
        dbexec('ALTER TABLE #__subscriptions_vats
        ADD `created_on` datetime DEFAULT NULL AFTER `data`,
        ADD `created_by` bigint(11) unsigned DEFAULT NULL AFTER `created_on`,
        ADD INDEX `created_by` (`created_by`),
        ADD `modified_on` datetime DEFAULT NULL AFTER `created_by`,
        ADD `modified_by` bigint(11) unsigned DEFAULT NULL AFTER `modified_on`,
        ADD INDEX `modified_by` (`modified_by`)  
        ');
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}