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
class ComAnahitaSchemaMigration22 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        $locationApp = dbfetch('SELECT * FROM `#__components` WHERE `option` = "com_locations"')[0];
        
        if ($locationApp['meta']) {
            $meta = json_decode($locationApp['meta']);
            if ($meta->browser_key) {
                $rawMeta = '{\"service\":\"google\",\"api_key\":\"'.$meta->browser_key.'\"}'; 
                dbexec('UPDATE `#__components` SET `meta` = "'.$rawMeta.'" WHERE `option` = "com_locations"');
            }
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