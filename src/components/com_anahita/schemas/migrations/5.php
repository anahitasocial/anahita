<?php

/**
 * LICENSE: ##LICENSE##.
 */

/**
 * Schema Migration.
 */
class ComAnahitaSchemaMigration5 extends ComMigratorMigrationVersion
{
    /**
     * Called when migrating up.
     */
    public function up()
    {
        $timeThen = microtime(true);

        //remove anahita from nodes and edges table names
        dbexec('RENAME TABLE #__anahita_nodes TO #__nodes');
        dbexec('RENAME TABLE #__anahita_edges TO #__edges');

        if(!dbexists('SHOW COLUMNS FROM `#__nodes` LIKE "verified"')) {
          dbexec("ALTER TABLE `#__nodes` ADD `verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `enabled`");
        }

        dbexec('ALTER TABLE #__nodes
                ADD `cover_filename` VARCHAR(255) NULL AFTER `filesize`,
                ADD `cover_filesize` INT(11) NULL AFTER `cover_filename`,
                ADD `cover_mimetype` VARCHAR(100) NULL AFTER `cover_filesize`');

        dbexec('ALTER TABLE #__nodes ADD `pinned` TINYINT(1) NOT NULL DEFAULT \'0\' AFTER `enabled`');

        //some legacy cleanup
        $legacyTables = array(
            'categories',
            'content',
            'content_frontpage',
            'core_log_items',
            'migration_backlinks',
            'migrations',
            'sections',
            'stats_agents',
            'tagmeta',
            'core_log_searches',
            'anahita_oauths');

        foreach ($legacyTables as $legacyTable) {
            dbexec('DROP TABLE IF EXISTS #__'.$legacyTable);
        }

        //delete a legacy record
        dbexec('DELETE FROM `#__components` WHERE `option` = \'com_mailto\' ');

        //add the hashtag contentfilter
        dbexec('INSERT INTO `#__plugins` (name,element,folder,iscore) VALUES (\'Hashtag\', \'hashtag\',\'contentfilter\',1)');

        //create the fields required for creating hashtag nodes
        dbexec('ALTER TABLE `#__nodes` DROP COLUMN `tag_count`');
        dbexec('ALTER TABLE `#__nodes` DROP COLUMN `tag_ids`');

        //install the hashtag related extensions
        dbexec('INSERT INTO #__components (`name`,`link`,`option`,`iscore`,`enabled`) VALUES (\'Hashtags\',\'option=com_hashtags\',\'com_hashtags\',1,1)');

        $ids = array();

/*
        //fetch only the nodes that contain something that resembels a hashtag
        $query_regexp = 'body REGEXP \'#([^0-9_\s\W].{2,})\'';

        dboutput("\nActors' Hashtags\n");
        //extracting hashtag terms from actors
        $ids = dbfetch('SELECT id FROM `#__nodes` WHERE type LIKE \'ComActorsDomainEntityActor%\' AND '.$query_regexp);

        foreach ($ids as $id) {
            $entity = KService::get('repos:actors.actor')->getQuery()->disableChain()->fetch($id);
            $hashtagTerms = $this->extractHashtagTerms($entity->description);

            foreach ($hashtagTerms as $term) {
                dboutput($term.', ');
                $entity->addHashtag($term)->save();
            }
        }

        dboutput("\nComments' hashtags\n");

        //extracting hashtag terms from comments
        $ids = dbfetch('SELECT id FROM `#__nodes` WHERE type LIKE \'ComBaseDomainEntityComment%\' AND '.$query_regexp);

        foreach ($ids as $id) {
            $entity = KService::get('com:base.domain.entity.comment')->getRepository()->getQuery()->disableChain()->fetch($id);
            $hashtagTerms = $this->extractHashtagTerms($entity->body);

            foreach ($hashtagTerms as $term) {
                dboutput($term.', ');
                $entity->addHashtag($term)->save();
            }
        }

        dboutput("\nMedia's Hashtags\n");
        //extracting hashtag terms from mediums: notes, topics, articles, and todos
        $query = 'SELECT id FROM `#__nodes` WHERE '.$query_regexp.' AND ( '.
                    'type LIKE \'%com:notes.domain.entity.note\' '.
                    'OR type LIKE \'%com:topics.domain.entity.topic\' '.
                    'OR type LIKE \'%com:photos.domain.entity.photo\' '.
                    'OR type LIKE \'%com:photos.domain.entity.set\' '.
                    'OR type LIKE \'%com:articles.domain.entity.article\' '.
                    'OR type LIKE \'%com:todos.domain.entity.todo\' '.
                    ' ) ';

        $ids = dbfetch($query);

        foreach ($ids as $id) {
            $entity = KService::get("repos:medium.medium")->getQuery()->disableChain()->find(array('id'=> $id));

            $hashtagTerms = $this->extractHashtagTerms($entity->description);

            foreach ($hashtagTerms as $term) {
                dboutput($term.', ');
                $entity->addHashtag($term)->save();
            }
        }
*/
        dbexec('UPDATE `#__plugins` SET published = 1 WHERE element = \'hashtag\'');

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

    /**
     * extracts a list of hashtag terms from a given text.
     *
     * @return array
     */
    private function extractHashtagTerms($text)
    {
        $matches = array();

        if (preg_match_all(ComHashtagsDomainEntityHashtag::PATTERN_HASHTAG, $text, $matches)) {
            return array_unique($matches[1]);
        } else {
            return array();
        }
    }
}
