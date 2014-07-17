<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @package    Com_Anahita
 * @subpackage Schema_Migration
 */

/**
 * Schema Migration
 *
 * @package    Com_Anahita
 * @subpackage Schema_Migration
 */
class ComAnahitaSchemaMigration5 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        $timeThen = microtime(true);
    	
    	//create the fields required for creating hashtag nodes
    	dbexec('ALTER TABLE #__anahita_nodes DROP COLUMN `tag_count`');
    	dbexec('ALTER TABLE #__anahita_nodes DROP COLUMN `tag_ids`');
        dbexec('ALTER TABLE #__anahita_nodes ADD `hashtagable_count` INT(11) UNSIGNED DEFAULT 0');
        dbexec('INSERT INTO #__plugins (name, element, folder) VALUES (\'Hashtag Filter\',\'hashtag\',\'contentfilter\')');
    	
        $ids = array();
        
        //fetch only the nodes that contain something that resembels a hashtag
        $query_regexp = 'body REGEXP \'#([^0-9_\s\W].{2,})\'';
        
        dboutput("\nActors' Hashtags\n");
    	//extracting hashtag terms from actors
        $ids = dbfetch('SELECT id FROM #__anahita_nodes WHERE type LIKE \'ComActorsDomainEntityActor%\' AND '.$query_regexp);

    	foreach($ids as $id)
    	{
    		$entity = KService::get('com://site/actors.domain.entity.actor')->getRepository()->getQuery()->disableChain()->fetch($id);    		
    		$hashtagTerms = $this->extractHashtagTerms($entity->description);
    		
    		foreach($hashtagTerms as $term)
    			if($entity->addHashtag($term)->save())
    				dboutput($term.', ');
    	}
        
    	dboutput("\nComments' hashtags\n");
    	//extracting hashtag terms from comments
    	$ids = dbfetch('SELECT id FROM #__anahita_nodes WHERE type LIKE \'ComBaseDomainEntityComment%\' AND '.$query_regexp);
    	
    	foreach($ids as $id)
    	{
    		$entity = KService::get('com://site/base.domain.entity.comment')->getRepository()->getQuery()->disableChain()->fetch($id);    		
    		$hashtagTerms = $this->extractHashtagTerms($entity->body);
    		
    		foreach($hashtagTerms as $term)
    			if($entity->addHashtag($term)->save())
    				dboutput($term.', ');
    	}
    	
    	dboutput("\nMedia's Hashtags\n");
    	//extracting hashtag terms from mediums: notes, topics, pages, and todos
    	$query = 	'SELECT id FROM #__anahita_nodes WHERE '.$query_regexp.' AND ( '.
    				'type LIKE \'%com:notes.domain.entity.note\' '.
    	 			'OR type LIKE \'%com:topics.domain.entity.topic\' '.
    	 			'OR type LIKE \'%com:photos.domain.entity.photo\' '.
    				'OR type LIKE \'%com:photos.domain.entity.set\' '.
    	 			'OR type LIKE \'%com:pages.domain.entity.page\' '.
    	 			'OR type LIKE \'%com:todos.domain.entity.todo\' '.
    	 			'OR type LIKE \'%com:milestone.domain.entity.milestone\' '.
    	 			' ) ';
    	
		$ids = dbfetch($query);
    	
    	foreach($ids as $id)
    	{
    		$entity = KService::get('com://site/medium.domain.entity.medium')->getRepository()->getQuery()->disableChain()->fetch($id);    		
    		
    		$hashtagTerms = $this->extractHashtagTerms($entity->description);
    		
    		foreach($hashtagTerms as $term)
    			if($entity->addHashtag($term)->save())
    				dboutput($term.', ');
    	}
    	
    	dboutput('Publish the hashtag plugin'."\n");
    	dbexec('UPDATE #__plugins SET published = 1 WHERE element = \'hashtag\'');
    	
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
    
	/**
	 * extracts a list of hashtag terms from a given text
	 * 
	 * @return array
	 */
	private function extractHashtagTerms($text)
	{
        $matches = array();
        
        if(preg_match_all(ComHashtagsDomainEntityHashtag::PATTERN_HASHTAG, $text, $matches))
        	return array_unique($matches[1]);
        else
        	return array();
	} 
}