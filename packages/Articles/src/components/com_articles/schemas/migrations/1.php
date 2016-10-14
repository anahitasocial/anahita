<?php

/**
 * Schema Migration
 *
 * @package    Com_Articles
 * @subpackage Schema_Migration
 */
class ComArticlesSchemaMigration1 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        //component
        dbexec('UPDATE `#__nodes` SET `component` = \'com_articles\' WHERE `component` = \'com_pages\' ');

        // node types
        dbexec('UPDATE `#__nodes` SET type=\'ComMediumDomainEntityMedium,ComArticlesDomainEntityArticle,com:articles.domain.entity.article\' WHERE type=\'ComMediumDomainEntityMedium,ComPagesDomainEntityPage,com:pages.domain.entity.page\'');

        // revision nodes
        dbexec('UPDATE `#__nodes` SET type=\'ComMediumDomainEntityMedium,ComArticlesDomainEntityRevision,com:articles.domain.entity.revision\' WHERE type=\'ComMediumDomainEntityMedium,ComPagesDomainEntityRevision,com:pages.domain.entity.revision\'');

        // comments
        dbexec('UPDATE `#__nodes` SET type=\'ComBaseDomainEntityComment,com:articles.domain.entity.comment\' WHERE type=\'ComBaseDomainEntityComment,com:pages.domain.entity.comment\' ');

        // parent types
        dbexec('UPDATE `#__nodes` SET parent_type=\'com:articles.domain.entity.article\' WHERE parent_type=\'com:pages.domain.entity.page\' ');

        // update permissions
        dbexec('UPDATE `#__nodes` SET `permissions` = REPLACE(`permissions`, \'com_pages:page\', \'com_articles:article\')');

        // story_object_type
        dbexec('UPDATE `#__nodes` SET story_object_type=\'com:articles.domain.entity.article\' WHERE story_object_type=\'com:pages.domain.entity.page\' ');

        // Update edges
        dbexec('UPDATE `#__edges` SET `node_b_type` = \'com:articles.domain.entity.article\' WHERE `node_b_type` = \'com:pages.domain.entity.page\' ');

		// Update stories and notifications
		dbexec('UPDATE `#__nodes` SET name=\'article_add\' WHERE name=\'page_add\'');
		dbexec('UPDATE `#__nodes` SET name=\'article_comment\' WHERE name=\'page_comment\'');
    }

   /**
    * Called when rolling back a migration
    */
    public function down()
    {
        //add your migration here
    }
}
