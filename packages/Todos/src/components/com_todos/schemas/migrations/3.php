<?php

/**
 * LICENSE: ##LICENSE##.
 */

/**
 * Schema Migration.
 */
class ComTodosSchemaMigration3 extends ComMigratorMigrationVersion
{
    /**
     * Called when migrating up.
     */
    public function up()
    {
        /*
        $timeThen = microtime(true);

        $db = KService::get('anahita:database');

        //change todo formats from html to string
        $entities = dbfetch('SELECT id, body FROM `#__nodes` WHERE type LIKE "%com:todos.domain.entity.todo" ');

        foreach ($entities as $entity) {
            $id = $entity['id'];
            $body = strip_tags($entity['body']);

            $db->update('nodes', array('body' => $body), ' WHERE id='.$id);
        }

        $timeDiff = microtime(true) - $timeThen;
        dboutput("TIME: ($timeDiff)"."\n");
        */
    }

    /**
     * Called when rolling back a migration.
     */
    public function down()
    {
        //add your migration here
    }
}
