<?php

/**
 * LICENSE: ##LICENSE##.
 */

/**
 * Schema Migration.
 */
class ComTopicsSchemaMigration2 extends ComMigratorMigrationVersion
{
    /**
     * Called when migrating up.
     */
    public function up()
    {
        //$timeThen = microtime(true);

        /*
        //converting the old boards as hashtags
        $boards = dbfetch('SELECT `id`, `alias` FROM #__nodes WHERE `type` LIKE \'%com:topics.domain.entity.board\' ');

        foreach ($boards as $board) {
            $terms = explode('-', $board['alias']);
            foreach ($terms as $index => $value) {
                if (strlen($value) < 3) {
                    unset($terms[$index]);
                }
            }

            $topics = KService::get('com:topics.domain.entity.topic')
                        ->getRepository()
                        ->getQuery()
                        ->disableChain()
                        ->where('parent_id = '.$board['id'])
                        ->fetchSet();

            foreach ($topics as $topic) {
                foreach ($terms as $term) {
                    if (strlen($term) > 3) {
                        dboutput($term.', ');
                        $topic->set('description', $topic->description.' #'.trim($term))->addHashtag($term)->save();
                    }
                }
            }
        }
        */

        dbexec('UPDATE `#__nodes` SET `parent_id` = 0 WHERE `type` LIKE \'%com:topics.domain.entity.topic\'');
        dbexec('DELETE FROM `#__nodes` WHERE `type` LIKE \'%com:topics.domain.entity.board\'');
        dbexec('DELETE FROM `#__edges` WHERE `node_b_type` LIKE \'%com:topics.domain.entity.board\'');
        dbexec('DROP TABLE #__topics_boards');

        //$timeDiff = microtime(true) - $timeThen;
        //dboutput("TIME: ($timeDiff)"."\n");
    }

    /**
     * Called when rolling back a migration.
     */
    public function down()
    {
        //add your migration here
    }
}
