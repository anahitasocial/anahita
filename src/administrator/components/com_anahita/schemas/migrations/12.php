<?php

/** 
 * LICENSE: ##LICENSE##.
 */

/**
 * Schema Migration.
 */
class ComAnahitaSchemaMigration12 extends ComMigratorMigrationVersion
{
    /**
     * Called when migrating up.
     */
    public function up()
    {
        dbexec('DELETE FROM #__anahita_edges WHERE node_a_id = node_b_id');
    }

    /**
     * Called when rolling back a migration.
     */
    public function down()
    {
        //add your migration here        
    }
}
