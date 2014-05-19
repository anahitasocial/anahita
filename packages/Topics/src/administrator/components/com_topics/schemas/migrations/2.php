<?php

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
class ComTopicsSchemaMigration2 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
		//converting the old boards as hashtags
    	$boards = dbfetch('SELECT id, alias FROM #__anahita_nodes WHERE TYPE LIKE \'%com:topics.domain.entity.board\' ');
    	
    	
        foreach($boards as $board)
        {
        	$terms = explode('-', $board['alias']);
        	foreach($terms as $index=>$value)
        		if(strlen($value) < 3)
        			unset($terms[$index]);
        						
        	$topics = KService::get('com://site/topics.domain.entity.topic')
        				->getRepository()
        				->getQuery()
        				->disableChain()
        				->where('parent_id = '.$board['id'])
        				->fetchSet();
        	
        	foreach($topics as $topic)
        	{
        		foreach($terms as $term)
        			if(strlen($term) > 3)
        				$topic->addHashtag($term)->save();
        	}
        }
        
        dbexec('UPDATE #__anahita_nodes SET parent_id = 0 WHERE type LIKE \'%com:topics.domain.entity.topic\'');
        dbexec('DELETE FROM #__anahita_nodes WHERE type LIKE \'%com:topics.domain.entity.board\'');
        dbexec('DELETE FROM #__anahita_edges WHERE node_b_type LIKE \'%com:topics.domain.entity.board\'');
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}