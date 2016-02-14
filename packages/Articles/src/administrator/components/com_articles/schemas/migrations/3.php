<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @package    Com_Articles
 * @subpackage Schema_Migration
 */

/**
 * Schema Migration
 *
 * @package    Com_Articles
 * @subpackage Schema_Migration
 */
class ComArticlesSchemaMigration3 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
		// Update nodes: type
        dbexec('UPDATE #__anahita_nodes SET type=\'ComMediumDomainEntityMedium,ComArticlesDomainEntityArticle,com:articles.domain.entity.article\' WHERE type=\'ComMediumDomainEntityMedium,ComPagesDomainEntityPage,com:pages.domain.entity.page\'');
        dbexec('UPDATE #__anahita_nodes SET type=\'ComMediumDomainEntityMedium,ComArticlesDomainEntityRevision,com:articles.domain.entity.revision\' WHERE type=\'ComMediumDomainEntityMedium,ComPagesDomainEntityRevision,com:pages.domain.entity.revision\'');

		// Update nodes: component
        dbexec('UPDATE #__anahita_nodes SET component=\'com_articles\' WHERE component=\'com_pages\'');

		// Update nodes: name
		dbexec('UPDATE #__anahita_nodes SET name=\'article_add\' WHERE name=\'page_add\'');
		dbexec('UPDATE #__anahita_nodes SET name=\'article_comment\' WHERE name=\'page_comment\'');
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}