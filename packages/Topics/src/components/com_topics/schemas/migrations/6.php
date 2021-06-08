<?php
use League\HTMLToMarkdown\HtmlConverter;

/**
 * LICENSE: ##LICENSE##
 *
 * @package    Com_Topics
 * @subpackage Schema_Migration
 */

/**
 * Schema Migration
 *
 * @package    Com_Topics
 * @subpackage Schema_Migration
 */
class ComTopicsSchemaMigration6 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        $timeThen = microtime(true);
        $converter = new HtmlConverter(array('strip_tags' => true));
        $db = AnService::get('anahita:database');
        
        $entities = dbfetch('SELECT id, body FROM `#__nodes` WHERE type LIKE "%com:topics.domain.entity.topic" ');
        
        foreach ($entities as $entity) {
            if ($entity['body']) {
                $id = $entity['id'];
                $body = $converter->convert($entity['body']);
                $body = str_replace('\#', '#', $body);
                $db->update('nodes', array('body' => $body), ' WHERE id='.$id);
            }
        }
        
        dboutput("Topics html converted to MarkDown!\n");
        $timeDiff = microtime(true) - $timeThen;
        dboutput("TIME: ($timeDiff)"."\n");
    }

   /**
    * Called when rolling back a migration
    */
    public function down()
    {
        //add your migration here
    }
}