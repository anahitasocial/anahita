<?php

/** 
 * LICENSE: ##LICENSE##.
 */

/**
 * Schema Migration.
 */
class ComAnahitaSchemaMigration4 extends ComMigratorMigrationVersion
{
    /**
     * Called when migrating up.
     */
    public function up()
    {
        dbexec('ALTER TABLE #__users ADD UNIQUE (`username`)');
        dbexec('ALTER TABLE #__users ADD UNIQUE (`email`)');
    }

    /**
     * Called when rolling back a migration.
     */
    public function down()
    {
        //add your migration here        
    }
}
