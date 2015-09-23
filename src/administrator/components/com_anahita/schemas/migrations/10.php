<?php

/**
 * LICENSE: ##LICENSE##.
 */

/**
 * Schema Migration.
 */
class ComAnahitaSchemaMigration10 extends ComMigratorMigrationVersion
{
    /**
     * Called when migrating up.
     */
    public function up()
    {
        //remove anahita from nodes and edges table names
        if (!dbexists('SHOW TABLES LIKE "#__nodes"')) {
            dbexec('RENAME TABLE #__anahita_nodes TO #__nodes');
        }

        if (!dbexists('SHOW TABLES LIKE "#__edges"')) {
            dbexec('RENAME TABLE #__anahita_edges TO #__edges');
        }

        if (!dbexists('SHOW COLUMNS FROM #__nodes LIKE "cover_filename"')) {
            dbexec('ALTER TABLE #__nodes
                ADD `cover_filename` VARCHAR(255) NULL AFTER `filesize`,
                ADD `cover_filesize` INT(11) NULL AFTER `cover_filename`,
                ADD `cover_mimetype` VARCHAR(100) NULL AFTER `cover_filesize`');
        }
    }

    /**
     * Called when rolling back a migration.
     */
    public function down()
    {
        //add your migration here
    }
}
