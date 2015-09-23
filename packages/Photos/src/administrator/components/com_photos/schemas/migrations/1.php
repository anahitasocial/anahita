<?php

/**
 * LICENSE: ##LICENSE##.
 */

/**
 * Schema Migration.
 */
class ComPhotosSchemaMigration1 extends ComMigratorMigrationVersion
{
    /**
     * Called when migrating up.
     */
    public function up()
    {
        //add your migration here
        dbexec("UPDATE jos_nodes SET filename = CONCAT(MD5(id),'.jpg') WHERE type LIKE '%com:photos.domain.entity.photo%' AND filename = ''");
    }

    /**
     * Called when rolling back a migration.
     */
    public function down()
    {
        //add your migration here
    }
}
