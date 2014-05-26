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
        
    	//create the fields required for creating hashtag nodes
    	dbexec('ALTER TABLE #__anahita_nodes DROP COLUMN `tag_count`');
        dbexec('ALTER TABLE #__anahita_nodes CHANGE `tag_ids` `hashtag_ids` TEXT DEFAULT NULL');
        dbexec('ALTER TABLE #__anahita_nodes ADD `hashtagable_count` INT(11) UNSIGNED DEFAULT NULL AFTER `hashtag_ids`');
        dbexec('ALTER TABLE #__anahita_nodes ADD `hashtagable_ids` TEXT DEFAULT NULL AFTER `hashtagable_count`');
        dbexec('INSERT INTO #__plugins (name,element,folder,published) VALUES (\'Hashtag Filter\',\'hashtag\',\'contentfilter\',1)');
    	
        $timeThen = microtime(true);
        $ids = array();
        
    	//extracting hashtag terms from actors
        $ids = dbfetch('SELECT id FROM #__anahita_nodes WHERE type LIKE \'ComActorsDomainEntityActor%\'');

    	foreach($ids as $id)
    	{
    		$entity = KService::get('com://site/actors.domain.entity.actor')->getRepository()->getQuery()->disableChain()->fetch($id);    		
    		$hashtagTerms = $this->extractHashtagTerms($entity->description);
    		
    		foreach($hashtagTerms as $term)
    			$entity->addHashtag(trim($term))->save();
    	}
        
    	//extracting hashtag terms from comments
    	$ids = dbfetch('SELECT id FROM #__anahita_nodes WHERE type LIKE \'ComBaseDomainEntityComment%\'');
    	
    	foreach($ids as $id)
    	{
    		$entity = KService::get('com://site/base.domain.entity.comment')->getRepository()->getQuery()->disableChain()->fetch($id);    		
    		$hashtagTerms = $this->extractHashtagTerms($entity->body);
    		
    		foreach($hashtagTerms as $term)
    			$entity->addHashtag(trim($term))->save();
    	}
    	
    	//extracting hashtag terms from mediums
		$ids = dbfetch('SELECT id FROM #__anahita_nodes WHERE type LIKE \'ComMediumDomainEntityMedium%\' AND type NOT LIKE \'%com:topics.domain.entity.board\' ');
    	
    	foreach($ids as $id)
    	{
    		$entity = KService::get('com://site/medium.domain.entity.medium')->getRepository()->getQuery()->disableChain()->fetch($id);    		
    		$hashtagTerms = $this->extractHashtagTerms($entity->description);
    		
    		foreach($hashtagTerms as $term)
    			$entity->addHashtag(trim($term))->save();
    	}
    	
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