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

        if (!dbexists('SHOW TABLES LIKE "#__edges"')) {
            dbexec('RENAME TABLE #__anahita_edges TO #__edges');
        }

        dbexec('DELETE FROM `#__edges` WHERE node_a_id = node_b_id');
    }

    /**
     * Called when rolling back a migration.
     */
    public function down()
    {
        //add your migration here
    }
}
