<?php

/**
 * LICENSE: ##LICENSE##
 *
 * @package    Com_Anahita
 * @subpackage Schema_Migration
 */

/**
 * This migration removes duplicate edges and enforces a unique constrain for multiple columns
 *
 * @package    Com_Anahita
 * @subpackage Schema_Migration
 */
class ComAnahitaSchemaMigration17 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        //fetch duplicate rows
        $duplicate_rows = dbfetch("SELECT *, COUNT(*) AS count FROM `#__edges` GROUP BY `type`, `node_a_id`, `node_a_type`, `node_b_id`, `node_b_type` HAVING count > 1");

        //delete duplicate rows
        foreach($duplicate_rows as $row) {
           dbexec('DELETE FROM `#__edges` WHERE `type`=\''.$row['type'].'\' AND `node_a_id`=\''.$row['node_a_id'].'\' AND `node_a_type`=\''.$row['node_a_type'].'\' AND `node_b_id`=\''.$row['node_b_id'].'\' AND `node_b_type`=\''.$row['node_b_type'].'\' ');
        }

        //add unique constraint to columns
        dbexec('ALTER TABLE `#__edges` ADD CONSTRAINT `uc_edge` UNIQUE(`type`, `node_a_id`, `node_a_type`, `node_b_id`, `node_b_type`)');

        //insert previously duplicate rows once each
        foreach($duplicate_rows as $row) {
           unset($row['id']);
           unset($row['count']);
           dbinsert('edges', $row);
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
