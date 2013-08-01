<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Anahita
 * @subpackage Schema_Migration
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Anahita Schema Migration
 *
 * @category   Anahita
 * @package    Com_Anahita
 * @subpackage Schema_Migration
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComAnahitaSchemaMigration1 extends ComMigratorMigrationVersion
{
    public function up()
    {
        anahita_20();
        anahita_21();
        anahita_22();
        anahita_23();
        anahita_24();
        anahita_25();
        anahita_26();

        dbexec('INSERT IGNORE INTO #__migrator_versions (component,version) VALUES 
                   ("connect",   1),
                   ("opensocial",1),
                   ("groups",0),
                   ("photos",0),
                   ("topics",1),
                   ("subscriptions",1),
                   ("todos",1)
                   ');
    }
    
}


//story migration
function anahita_20()
{
    //bug fix. delete any edge associated with stories that are not story_add, private_message
    dbexec("delete e.* from jos_anahita_edges as e inner join jos_anahita_nodes as n on n.id = e.node_b_id where node_b_type like 'com:stories.domain.entity.story' and (n.name != 'story_add' and n.name != 'private_message') ");
    dbexec("UPDATE jos_anahita_edges as e,jos_anahita_nodes as n SET e.node_b_type = 'com:notes.domain.entity.note' WHERE n.id = e.node_b_id and node_b_type like 'com:stories.domain.entity.story'");

    $set = array(
            '`type` = "ComMediumDomainEntityMedium,ComNotesDomainEntityNote,com:notes.domain.entity.note"' ,
            '`name` = ""',
            '`alias` = ""',
            '`component` = "com_notes"',
    );
    $set   = implode($set,',');
    $query = 'UPDATE jos_anahita_nodes SET '.$set." where type like 'ComMediumDomainEntityMedium,ComStoriesDomainEntityStory,com:stories.domain.entity.story' and (name = 'story_add' or name = 'private_message')";
    dbexec($query);

    //create the stories for notes
    $query = "insert into jos_anahita_nodes(type,component,name,owner_id,owner_type, story_subject_id, story_object_type, story_object_id, story_target_id,meta,created_on,created_by,modified_on,modified_by) select 'ComStoriesDomainEntityStory,com:stories.domain.entity.story' as type,'com_notes','note_add',owner_id,owner_type,story_subject_id, 'com:notes.domain.entity.note' AS story_object_type,id AS story_object_id,story_target_id,meta,created_on,created_by,modified_on,modified_by from jos_anahita_nodes where type like 'ComMediumDomainEntityMedium,ComNotesDomainEntityNote,com:notes.domain.entity.note'";
    dbexec($query);

    //convert the notifications to point to the note
    $query = "update jos_anahita_nodes as nf, jos_anahita_nodes as note set nf.component = 'com_notes', nf.story_object_type = 'com:notes.domain.entity.note', nf.name = IF(nf.name='story_comment','note_comment',IF(nf.name='story_add' or nf.name='private_message','note_add',nf.name)) where note.id = nf.story_object_id and note.type like 'ComMediumDomainEntityMedium,ComNotesDomainEntityNote,com:notes.domain.entity.note' and nf.story_object_type like 'com:stories.domain.entity.story' and nf.type like 'ComNotificationsDomainEntityNotification,com:notifications.domain.entity.notification'";
    dbexec($query);

    //set the story data in the notes to null
    $query = "update jos_anahita_nodes set story_subject_id = null,story_object_type=null,story_object_id=null,story_target_id=null where type like 'ComMediumDomainEntityMedium,ComNotesDomainEntityNote,com:notes.domain.entity.note'";
    dbexec($query);

    //convert the parent_type of comments
    $query = "update jos_anahita_nodes as comment, jos_anahita_nodes as note set comment.type = 'ComBaseDomainEntityComment,com:notes.domain.entity.comment', comment.parent_type = 'com:notes.domain.entity.note' where note.id = comment.parent_id and comment.type like 'ComBaseDomainEntityComment,%' and comment.parent_type like 'com:stories.domain.entity.story'";
    dbexec($query);

    //delete any dangling comment
    $query = "delete comment from jos_anahita_nodes as comment left join jos_anahita_nodes as note on note.id = comment.parent_id where comment.type like 'ComBaseDomainEntityComment,%' and note.id IS NULL";
    dbexec($query);

    //delete any remaining story edge. storeis shouldn't have any edge (since they are no longer subscribable,votable)
    $query = "delete edge.* from jos_anahita_edges as edge where node_b_type like 'com:stories.domain.entity.story'";
    dbexec($query);
     
    //somehow we have stories for commenting on a sotry we need to delete those
    $query = "delete edge.* from jos_anahita_edges as edge where node_a_id IN (select id from jos_anahita_nodes where story_object_type like 'com:stories.domain.entity.story') or node_b_id IN (select id from jos_anahita_nodes where story_object_type like 'com:stories.domain.entity.story')";
    dbexec($query);

    $query = "delete from jos_anahita_nodes where story_object_type like 'com:stories.domain.entity.story'";
    dbexec($query);

    // story doesn't have any subscribable, commentable,describable, privatable, votable behavior
    // set the respective columns to null
    //last but not least set all the story type as non-medium
    $query = "update jos_anahita_nodes set type = 'ComStoriesDomainEntityStory,com:stories.domain.entity.story', subscriber_count = null,subscriber_ids=null,comment_status=null,comment_count=null,alias=null,voter_up_ids=null,voter_down_ids=null,vote_up_count=null,vote_down_count=null,access=null,permissions=null where type like 'ComMediumDomainEntityMedium,ComStoriesDomainEntityStory,com:stories.domain.entity.story'";
    dbexec($query);

    //insert notes component
    dbexec("INSERT INTO `jos_components` VALUES(null, 'Notes', 'option=com_notes', 0, 0, '', 'Notes', 'com_notes', 0, '', 1, '', 1)");

    dbexec("UPDATE jos_anahita_nodes SET permissions = REPLACE(permissions,'com_stories:story','com_notes:note') WHERE type LIKE 'ComActorsDomainEntityActor%' ");

    //delete legacy plugins
    dbexec("DELETE from jos_plugins WHERE CONCAT_WS('.',folder,element) IN ('system.legacy','system.cache','system.mtupgrade','system.remember','system.mailer','content.emailcloak','content.loadmodule','content.pagenavigation','search.content','search.categories','search.sections','system.log')");
    dbexec("DELETE from jos_modules WHERE client_id = 1 AND module IN ('mod_rokquicklinks','mod_popular','mod_latest','mod_unread','mod_online','mod_logged','mod_footer','mod_status','mod_quickicon','mod_feed','mod_title','mod_toolbar')");

}

//tag migration
function anahita_21()
{
    dbexec('ALTER TABLE jos_anahita_nodes
                ADD tag_count INT(11) UNSIGNED NULL AFTER blocked_ids,
                ADD tag_ids TEXT NULL AFTER tag_count');

}

function anahita_22()
{
    dbexec('UPDATE jos_modules SET module = "mod_menu" WHERE module LIKE "mod_mainmenu"');
}

function anahita_23()
{
    dbexec("insert into jos_anahita_nodes (component,type,owner_id,owner_type)
select app.component,'ComComponentsDomainEntityAssignment,com:components.domain.entity.assignment',enable.node_a_id,enable.node_a_type from jos_anahita_edges as enable
inner join jos_anahita_nodes as app on app.id = enable.node_b_id
where enable.type like 'ComAppsDomainEntityEnable,com:apps.domain.entity.enable'");
    dbexec("insert into jos_anahita_nodes (component,type,name,access)
select app.component,'ComComponentsDomainEntityAssignment,com:components.domain.entity.assignment',actortype.name,enable.meta from jos_anahita_edges as enable
inner join jos_anahita_nodes as app on app.id = enable.node_b_id
inner join jos_anahita_nodes as actortype on actortype.id = enable.node_a_id
where enable.type like 'ComAppsDomainEntityAssignment,com:apps.domain.entity.assignment'
");
    //add optional access
    $query = <<<EOF
	insert into jos_anahita_nodes (component,type,name,access)
select distinct node.component,'ComComponentsDomainEntityAssignment,com:components.domain.entity.assignment',edge1.node_a_type,0
from jos_anahita_nodes as node
 inner join jos_anahita_edges as edge1 on edge1.node_b_id = node.id and edge1.type like 'ComAppsDomainEntityEnable,com:apps.domain.entity.enable'
 left join jos_anahita_edges as edge on edge.node_b_id = node.id and edge.type like 'ComAppsDomainEntityAssignment,com:apps.domain.entity.assignment'
 where node.type like 'ComAppsDomainEntityApp,com:apps.domain.entity.app' and edge.node_a_id IS NULL
;
EOF;
    dbexec($query);
    //add com_notes as always to all actortypes
    dbexec("insert into jos_anahita_nodes(component,type,name,access) select distinct 'com_notes','ComComponentsDomainEntityAssignment,com:components.domain.entity.assignment',node_a_type,'1' from jos_anahita_edges where type like 'ComAppsDomainEntityEnable,com:apps.domain.entity.enable'");
	dbexec("delete from jos_anahita_nodes where type like 'ComApps%'");
	dbexec("delete from jos_anahita_edges where type like 'ComApps%'");

	dbexec("delete from jos_components where `option` like 'com_apps'");
	dbexec("INSERT INTO `jos_components` VALUES(0, 'Components', 'option=com_components', 0, 0, 'option=com_components', 'Components', 'com_components', 0, 'js/ThemeOffice/component.png', 1, '', 1);");
    //order the assignable components
	dbexec("set @order := 0");
	dbexec("update jos_components set ordering = (@order := @order + 1) where parent = 0 and `option` IN (SELECT distinct component from jos_anahita_nodes where type LIKE 'ComComponentsDomainEntityAssignment,com:components.domain.entity.assignment')");
}

function anahita_24()
{
	dbexec("DELETE FROM jos_modules_menu WHERE moduleid IN (SELECT id FROM jos_modules WHERE module IN ('mod_search', 'mod_feed','mod_login', 'mod_search', 'mod_breadcrumbs', 'mod_sections', 'mod_syndicate', 'mod_latestnews', 'mod_newsflash', 'mod_related_items'))");
	dbexec("DELETE FROM jos_modules WHERE module IN ('mod_search', 'mod_feed', 'mod_login', 'mod_breadcrumbs', 'mod_sections', 'mod_syndicate', 'mod_latestnews', 'mod_newsflash', 'mod_related_items')");
}

function anahita_25()
{
    dbexec("create index group_id on jos_core_acl_groups_aro_map (group_id)");
    dbexec("create index aro_id on jos_core_acl_groups_aro_map (aro_id)");
    dbexec("create index value on jos_core_acl_aro (value)");

    dbexec("INSERT INTO `jos_components` VALUES(0, 'Mailer', 'option=com_mailer', 0, 0, 'option=mailer', 'Mailer', 'com_mailer', 0, 'js/ThemeOffice/component.png', 1, '', 1);");
    /*
    dbexec("alter table jos_anahita_nodes change access access varchar(50) NULL");
    dbexec("alter table jos_anahita_nodes add access_list text null after access");
    dbexec("update jos_anahita_nodes set access_list = access where (type like 'ComMediumDomainEntityMedium%' or type like 'ComActorsDomainEntityActor%') and access <> ''")
    dbexec("update jos_anahita_nodes set access = 'custom' where access not in ('public','registered','special','followers','leaders','mutuals','admins') and (type like 'ComMediumDomainEntityMedium%' or type like 'ComActorsDomainEntityActor%') and access <> ''")
    */
}

function anahita_26()
{
    dbexec('update jos_anahita_nodes as n inner join jos_users as u on u.id = n.person_userid set n.person_username = u.username where u.username <> n.person_username');
    //header
    dbexec("UPDATE jos_modules SET `position` = REPLACE(`position`, 'header', '1') WHERE `position` LIKE 'header%' ");
    
    //showcase
    dbexec("UPDATE jos_modules SET `position` = REPLACE(`position`, 'showcase', '1') WHERE `position` LIKE 'showcase%' ");
    
    //utility
    dbexec("UPDATE jos_modules SET `position` = REPLACE(`position`, 'utility', '1') WHERE `position` LIKE 'utility%' ");
    
    //maintop
    dbexec("UPDATE jos_modules SET `position` = REPLACE(`position`, 'maintop', '4') WHERE `position` LIKE 'maintop%' ");
    
    //mainbottom
    dbexec("UPDATE jos_modules SET `position` = REPLACE(`position`, 'mainbottom', '4') WHERE `position` LIKE 'mainbottom%' ");
    
    //bottom
    dbexec("UPDATE jos_modules SET `position` = REPLACE(`position`, 'bottom', '5') WHERE `position` LIKE 'bottom%' ");
    
    //footer
    dbexec("UPDATE jos_modules SET `position` = REPLACE(`position`, 'footer', '5') WHERE `position` LIKE 'footer%' ");
}
