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
class ComAnahitaSchemaMigration9 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        //looks like these two didn't work in previous migrations    
        dbexec("DROP TABLE #__content_rating");
        dbexec("DELETE FROM #__components WHERE `option` IN  ('com_media', 'com_menus', 'com_modules')");
        
        //add github gist plugin
        dbexec("INSERT INTO `#__plugins` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES (49, 'Content Filter - GithubGist', 'gist', 'contentfilter', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '')");
        
        //remove the syntax plugin
        dbexec("DELETE FROM #__plugins WHERE `element` IN ('syntax', 'ptag') ");
        
        //UTF-8 conversions
        dbexec("ALTER DATABASE CHARACTER SET utf8");
        dbexec("ALTER TABLE #__anahita_edges CHARACTER SET utf8");
        dbexec("ALTER TABLE #__anahita_nodes CHARACTER SET utf8");
        
        dbexec("ALTER TABLE #__anahita_nodes CHANGE name name VARBINARY");
        dbexec("ALTER TABLE #__anahita_nodes CHANGE name name VARCHAR");
        
        dbexec("ALTER TABLE #__anahita_nodes CHANGE alias alias VARBINARY");
        dbexec("ALTER TABLE #__anahita_nodes CHANGE alias alias VARCHAR");
        
        dbexec("ALTER TABLE #__anahita_nodes CHANGE body body MEDIUMBLOB");
        dbexec("ALTER TABLE #__anahita_nodes CHANGE body body MEDIUMBLOB");
        
        dbexec("ALTER TABLE #__anahita_nodes CHANGE person_given_name person_given_name VARBINARY");
        dbexec("ALTER TABLE #__anahita_nodes CHANGE person_given_name person_given_name VARCHAR");
        
        dbexec("ALTER TABLE #__anahita_nodes CHANGE person_family_name person_family_name VARBINARY");
        dbexec("ALTER TABLE #__anahita_nodes CHANGE person_family_name person_family_name VARCHAR");
        
        dbexec("ALTER TABLE #__migrator_migrations CHARACTER SET utf8");
        dbexec("ALTER TABLE #__migrator_versions CHARACTER SET utf8");
        dbexec("ALTER TABLE #__opensocial_profiles CHARACTER SET utf8");
        dbexec("ALTER TABLE #__opensocial_profiles CHARACTER SET utf8");
        
        //move these to related components
        dbexec("ALTER TABLE #__invites_tokens CHARACTER SET utf8");
        dbexec("ALTER TABLE #__opensocial_profiles CHARACTER SET utf8");
        dbexec("ALTER TABLE #__subscriptions_coupons CHARACTER SET utf8");
        dbexec("ALTER TABLE #__subscriptions_packages CHARACTER SET utf8");
        dbexec("ALTER TABLE #__subscriptions_transactions CHARACTER SET utf8");
        dbexec("ALTER TABLE #__subscriptions_vats CHARACTER SET utf8");
        dbexec("ALTER TABLE #__todos_todos CHARACTER SET utf8");
        dbexec("ALTER TABLE #__topics_topics CHARACTER SET utf8");
        
        dbexec("ALTER TABLE #__users CHARACTER SET utf8");
        
        dbexec("ALTER TABLE #__users CHANGE name name VARBINARY");
        dbexec("ALTER TABLE #__users CHANGE name name VARCHAR");
        
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}