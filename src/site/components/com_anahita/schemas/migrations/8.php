<?php

/** 
 * LICENSE: ##LICENSE##.
 */

/**
 * Schema Migration.
 */
class ComAnahitaSchemaMigration8 extends ComMigratorMigrationVersion
{
    /**
     * Called when migrating up.
     */
    public function up()
    {
        //dropping module tables
        dbexec('DROP TABLE IF EXISTS #__modules');
        dbexec('DROP TABLE IF EXISTS #__modules_menu');
    }

    /**
     * Called when rolling back a migration.
     */
    public function down()
    {
        //add your migration here        
    }
}
