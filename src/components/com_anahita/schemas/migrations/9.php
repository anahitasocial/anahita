<?php

/**
 * LICENSE: ##LICENSE##.
 */

/**
 * Schema Migration.
 */
class ComAnahitaSchemaMigration9 extends ComMigratorMigrationVersion
{
    /**
     * Called when migrating up.
     */
    public function up()
    {
        //looks like these two didn't work in previous migrations
        dbexec('DROP TABLE `#__content_rating`');
        dbexec("DELETE FROM `#__components` WHERE `option` IN  ('com_media', 'com_menus', 'com_modules')");

        //add github gist plugin
        dbexec("INSERT INTO `#__plugins` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES (49, 'Content Filter - GithubGist', 'gist', 'contentfilter', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '')");

        //remove the syntax plugin
        dbexec("DELETE FROM `#__plugins` WHERE `element` IN ('syntax', 'ptag') ");

        //remove anahita from nodes and edges table names
        if (!dbexists('SHOW TABLES LIKE "#__nodes"')) {
            dbexec('RENAME TABLE #__anahita_nodes TO #__nodes');
        }

        if (!dbexists('SHOW TABLES LIKE "#__edges"')) {
            dbexec('RENAME TABLE #__anahita_edges TO #__edges');
        }

        //UTF-8 conversions
        dbexec('ALTER DATABASE CHARACTER SET utf8');
        dbexec('ALTER TABLE `#__edges` CHARACTER SET utf8');
        dbexec('ALTER TABLE `#__nodes` CHARACTER SET utf8');

        dbexec('ALTER TABLE `#__nodes` CHANGE name name VARBINARY(255)');
        dbexec('ALTER TABLE `#__nodes` CHANGE name name VARCHAR(255) CHARACTER SET utf8');

        dbexec('ALTER TABLE `#__nodes` CHANGE alias alias VARBINARY(255)');
        dbexec('ALTER TABLE `#__nodes` CHANGE alias alias VARCHAR(255) CHARACTER SET utf8');

        dbexec('ALTER TABLE `#__nodes` CHANGE body body MEDIUMBLOB');
        dbexec('ALTER TABLE `#__nodes` CHANGE body body MEDIUMTEXT CHARACTER SET utf8mb4');

        dbexec('ALTER TABLE `#__nodes` CHANGE excerpt excerpt TEXT CHARACTER SET utf8mb4');

        dbexec('ALTER TABLE `#__nodes` CHANGE person_given_name person_given_name VARBINARY(255)');
        dbexec('ALTER TABLE `#__nodes` CHANGE person_given_name person_given_name VARCHAR(255) CHARACTER SET utf8');

        dbexec('ALTER TABLE `#__nodes` CHANGE person_family_name person_family_name VARBINARY(255)');
        dbexec('ALTER TABLE `#__nodes` CHANGE person_family_name person_family_name VARCHAR(255) CHARACTER SET utf8');

        dbexec('ALTER TABLE `#__migrator_migrations` CHARACTER SET utf8');
        dbexec('ALTER TABLE `#__migrator_versions` CHARACTER SET utf8');
        dbexec('ALTER TABLE `#__opensocial_profiles` CHARACTER SET utf8');
        dbexec('ALTER TABLE `#__opensocial_profiles` CHARACTER SET utf8');

        //move these to related components
        dbexec('ALTER TABLE `#__invites_tokens` CHARACTER SET utf8');
        dbexec('ALTER TABLE `#__opensocial_profiles` CHARACTER SET utf8');
        dbexec('ALTER TABLE `#__subscriptions_coupons` CHARACTER SET utf8');
        dbexec('ALTER TABLE `#__subscriptions_packages` CHARACTER SET utf8');
        dbexec('ALTER TABLE `#__subscriptions_transactions` CHARACTER SET utf8');
        dbexec('ALTER TABLE `#__subscriptions_vats` CHARACTER SET utf8');
        dbexec('ALTER TABLE `#__todos_todos` CHARACTER SET utf8');
        dbexec('ALTER TABLE `#__topics_topics` CHARACTER SET utf8');

        dbexec('ALTER TABLE `#__users` CHARACTER SET utf8');

        dbexec('ALTER TABLE `#__users` CHANGE name name VARBINARY(255)');
        dbexec('ALTER TABLE `#__users` CHANGE name name VARCHAR(255) CHARACTER SET utf8');

        //updating comments

        $timeThen = microtime(true);
        $db = KService::get('anahita:database');

        //change comment formats from html to string
        $entities = dbfetch('SELECT id, body FROM `#__nodes` WHERE type LIKE "ComBaseDomainEntityComment%" ');

        dboutput("Updating comments. This WILL take a while ...\n");

        foreach ($entities as $entity) {
            $id = $entity['id'];
            $body = strip_tags($entity['body']);

            $db->update('nodes', array('body' => $body), ' WHERE id='.$id);
        }

        dboutput("Comments updated!\n");

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
