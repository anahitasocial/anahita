<?php

/**
 * LICENSE: ##LICENSE##.
 */

/**
 * Schema Migration.
 */
class ComTodosSchemaMigration2 extends ComMigratorMigrationVersion
{
    /**
     * Called when migrating up.
     */
    public function up()
    {
        $timeThen = microtime(true);

        dbexec('DELETE FROM `#__nodes` WHERE `type` LIKE \'%ComBaseDomainEntityComment%\' AND `parent_type` = \'com:todos.domain.entity.milestone\' ');

        dbexec('DELETE FROM `#__nodes` WHERE `type` LIKE \'%com:todos.domain.entity.milestone\' ');

        dbexec('DELETE FROM `#__edges` WHERE `node_b_type` LIKE \'%com:todos.domain.entity.milestone\' ');

        dbexec('DROP TABLE #__todos_milestones');

        /*
        //clearing todolists from the data
        $todolists = dbfetch('SELECT `id`, `parent_id`, `alias` FROM `#__nodes` WHERE `type` LIKE \'%com:todos.domain.entity.todolist\' ');

        foreach ($todolists as $todolist) {
            $terms = explode('-', $todolist['alias']);
            foreach ($terms as $index => $value) {
                if (strlen($value) < 3) {
                    unset($terms[$index]);
                }
            }

            $todos = KService::get('com:todos.domain.entity.todo')
                    ->getRepository()
                    ->getQuery()
                    ->disableChain()
                    ->where('parent_id = '.$todolist['id'])
                    ->fetchSet();

            foreach ($todos as $todo) {
                foreach ($terms as $term) {
                    if (strlen($term) > 3) {
                        dboutput($term.', ');
                        $todo->set('parent_id', 0)->set('description', $todo->description.' #'.trim($term))->addHashtag($term)->save();
                    }
                }
            }
        }
        */

        dbexec('DELETE FROM `#__nodes` WHERE `type` LIKE \'%com:todos.domain.entity.todolist\' ');

        //clear stories
        dbexec('DELETE FROM `#__nodes` WHERE `story_object_type` = \'com:todos.domain.entity.todolist\' OR `story_object_type` = \'com:todos.domain.entity.milestone\' ');

        dbexec('DROP TABLE #__todos_todolists');

        $timeDiff = microtime(true) - $timeThen;
        dboutput("TIME: ($timeDiff)"."\n");
    }

    /**
     * Called when rolling back a migration.
     */
    public function down()
    {
        //add your migration here
    }
}
