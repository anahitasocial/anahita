<?php 

function topics_1()
{       
    dbexec("UPDATE jos_anahita_nodes SET `name` = 'topic_add'  WHERE `component` LIKE 'com_discussions' AND `name` LIKE 'new_topic'");
    dbexec("DELETE FROM jos_anahita_nodes WHERE `component` LIKE 'com_discussions' AND `name` IN ('edit_topic','new_reply','new_board')");
    
    //migrate nodes: type, parent_type, owner_type, story_object_type, component
    dbexec("UPDATE `jos_anahita_nodes` SET 
                `parent_type`       = REPLACE(`parent_type`, 'com.discussions', 'com.topics'),
                `story_object_type` = REPLACE(`story_object_type`, 'com.discussions', 'com.topics'), 
                `type`              = REPLACE(REPLACE(`type`, 'ComDiscussions', 'ComTopics'),'com.discussions','com.topics'),
                `component`         = 'com_topics' WHERE `component` LIKE 'com_discussions' ");
        
    dbexec("UPDATE `jos_anahita_nodes` SET `permissions` = REPLACE(permissions,'com_discussions','com_topics') 
            WHERE `permissions` LIKE '%com_discussions%' AND `type` LIKE 'AnSeEntityNode,AnSeEntityActor%'");
    
    //migrate edges
    dbexec("UPDATE `jos_anahita_edges` SET `node_b_type` = REPLACE(node_b_type, 'com.discussions', 'com.topics') WHERE `type` LIKE '%com.discussions%' ");
    dbexec("UPDATE `jos_anahita_edges` SET `component` = 'com_topics' WHERE `component` LIKE 'com_discussions' ");
    
    //migrate topics_topics
    if(dbexists("SHOW TABLES LIKE 'jos_discussions_topics'"))
    {
        dbexec("DROP TABLE IF EXISTS `jos_topics_topics` ");
        dbexec("RENAME TABLE `jos_discussions_topics` TO `jos_topics_topics`");
        dbexec("ALTER TABLE `jos_topics_topics` CHANGE `discussions_topic_id` `id` BIGINT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT");
    }
    
    //migrate topics_boards. Why not we are going to convert them to tags later on anyway!
    if(dbexists("SHOW TABLES LIKE 'jos_discussions_boards'"))
    {
        dbexec("DROP TABLE IF EXISTS `jos_topics_boards` ");
        dbexec("RENAME TABLE `jos_discussions_boards` TO `jos_topics_boards`");
        dbexec("ALTER TABLE `jos_topics_boards` CHANGE `discussions_board_id` `topics_board_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT");
    }
    
    if ( php_sapi_name() == 'cli' )
    {
        //migrate the component table
        dbexec("DELETE FROM `jos_components` WHERE `option`='com_discussions'");        
    } 
    else 
    {
        jimport('joomla.installer.installer');
        //unisntall the discussions
        $id = dbfetch('SELECT id FROM jos_components WHERE `option` LIKE "com_discussions"',KDatabase::FETCH_FIELD);
        if ( $id )
        {            
            JInstaller::getInstance()->uninstall('component', $id);            
        }
    }
}