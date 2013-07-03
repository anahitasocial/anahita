
//Set modified on to created on
UPDATE jos_anahita_nodes SET modified_on = created_on WHERE modified_on = '0000-00-00 00:00:00';
UPDATE jos_anahita_edges SET modified_on = created_on WHERE modified_on = '0000-00-00 00:00:00';

//Node Table Changes
ALTER TABLE jos_anahita_nodes 
    DROP unique_alias, 
    DROP url, 
    DROP tag_status, 
    DROP tag_count, 
    DROP last_tagged_by, 
    DROP last_tagged_on, 
    DROP medium_tree_path, 
    DROP medium_tree_level, 
    DROP medium_meta_keywords, 
    DROP medium_meta_description, 
    DROP medium_ping_status, 
    DROP medium_to_ping, 
    DROP medium_pinged, 
    DROP story_is_read,
    ADD follower_count INT(11) UNSIGNED  NULL AFTER status_update_time,
    ADD leader_count INT(11) UNSIGNED NULL AFTER follower_count,
    ADD mutual_count INT(11) UNSIGNED NULL AFTER leader_count,    
    ADD leader_ids   MEDIUMTEXT NULL  AFTER mutual_count,        
    ADD follower_ids MEDIUMTEXT NULL  AFTER leader_ids,
    ADD mutual_ids MEDIUMTEXT NULL    AFTER   follower_ids, 
    ADD blocker_ids TEXT NULL  AFTER mutual_ids,
    ADD blocked_ids TEXT NULL  AFTER blocker_ids,
    ADD subscriber_count INT(11) UNSIGNED NULL AFTER blocker_ids,
    ADD subscriber_ids MEDIUMTEXT NULL AFTER subscriber_count,    
    ADD administrating_ids TEXT NULL AFTER subscriber_ids,
    ADD administrator_ids TEXT NULL AFTER administrating_ids,
    ADD vote_up_count INT(11) UNSIGNED  NULL AFTER administrating_ids,
    ADD vote_down_count INT(11) UNSIGNED  NULL AFTER vote_up_count,
    ADD voter_up_ids MEDIUMTEXT NULL AFTER vote_down_count,
    ADD voter_down_ids MEDIUMTEXT NULL AFTER voter_up_ids,
    ADD shared_owner_count INT(11) UNSIGNED NULL AFTER voter_down_ids,
    ADD shared_owner_ids MEDIUMTEXT NULL AFTER shared_owner_count,
    CHANGE medium_excerpt excerpt TEXT CHARACTER SET `utf8` COLLATE `utf8_general_ci` NULL,
    CHANGE medium_mime_type mimetype VARCHAR(100)  NULL,
    CHANGE actor_thumbnail_url filename VARCHAR(255) NULL,
    ADD    filesize INT NULL AFTER filename,
    CHANGE component component VARCHAR(100) NOT NULL,
    CHANGE type type VARCHAR(255) NOT NULL,
    CHANGE name name  VARCHAR(255) CHARACTER SET `utf8` COLLATE `utf8_general_ci` NULL,
    CHANGE alias alias VARCHAR(255)  CHARACTER SET `utf8` COLLATE `utf8_general_ci` NULL,
    CHANGE body body MEDIUMTEXT CHARACTER SET `utf8` COLLATE `utf8_general_ci` NULL,
    CHANGE comment_status comment_status TINYINT(1) NULL,
    CHANGE comment_count comment_count INT(11) UNSIGNED NULL,
    ADD    last_comment_id BIGINT(11) UNSIGNED NULL AFTER comment_count,    
    CHANGE ordering ordering SMALLINT(11) SIGNED NULL,
    CHANGE last_comment_by last_comment_by BIGINT(11) UNSIGNED NULL,
    CHANGE hits hits INT(11) UNSIGNED NULL,
    CHANGE published enabled TINYINT(1) NULL,
    CHANGE last_comment_on last_comment_on DATETIME NULL,
    CHANGE parent_id parent_id BIGINT(11) UNSIGNED  NULL,
    CHANGE owner_id owner_id BIGINT(11) UNSIGNED NULL,
    CHANGE geo_latitude  geo_latitude  float(10,6) NULL,
    CHANGE geo_longitude geo_longitude float(10,6) NULL,
    CHANGE created_on created_on DATETIME NULL,
    CHANGE created_by created_by BIGINT(11) UNSIGNED  NULL,
    CHANGE modified_on modified_on DATETIME NULL,
    CHANGE modified_by modified_by BIGINT(11) UNSIGNED  NULL,
    CHANGE actor_gender actor_gender VARCHAR(50) NULL,
    CHANGE actor_latest_story status TEXT CHARACTER SET `utf8` COLLATE `utf8_general_ci` NULL,
    CHANGE actor_latest_story_created_on status_update_time DATETIME NULL,
    CHANGE story_object_type story_object_type VARCHAR(255) NULL,
    CHANGE privacy_read access TEXT NULL,
    CHANGE privacy_data permissions TEXT NULL,
    CHANGE meta meta TEXT NULL,
    ADD    story_comment_id INT(11) UNSIGNED  NULL AFTER story_target_id,
    DEFAULT CHARACTER SET latin1
    ;
    
//edges table    
ALTER TABLE jos_anahita_edges
    CHANGE type type VARCHAR(255) NOT NULL,
    CHANGE component component VARCHAR(100) NOT NULL,
    CHANGE node_a_id node_a_id BIGINT(11) UNSIGNED NOT NULL,
    CHANGE node_b_id node_b_id BIGINT(11) UNSIGNED NOT NULL,
    CHANGE node_a_type node_a_type VARCHAR(255) NOT NULL,
    CHANGE node_b_type node_b_type VARCHAR(255) NOT NULL,
    CHANGE created_by  created_by  BIGINT(11) UNSIGNED NULL,
    CHANGE modified_by modified_by BIGINT(11) UNSIGNED NULL,
    CHANGE created_on  created_on  DATETIME NULL,
    CHANGE modified_on modified_on DATETIME NULL,
    CHANGE meta meta TEXT NULL,
    ADD    ordering INT(11) UNSIGNED NULL,
    ADD    start_date DATETIME NULL,
    ADD    end_date DATETIME NULL,
    DROP   directed,
    DEFAULT CHARACTER SET latin1
    ;

//set the max group_concat
SET SESSION group_concat_max_len = @@max_allowed_packet;

//setting stats
UPDATE jos_anahita_nodes SET story_comment_id = (substring(meta,locate(':',meta)+1,IF(locate(',',meta)=0, locate('}',meta), locate(',',meta)) - locate(':',meta) - 1 ) ) WHERE type LIKE '%story%' AND meta LIKE '%comment_id%';        
UPDATE jos_anahita_nodes AS n SET follower_count = (SELECT count(e.node_a_id) FROM jos_anahita_edges AS e WHERE e.node_b_id = n.id AND type LIKE '%follow%' ), leader_count = (SELECT count(e.node_b_id) FROM jos_anahita_edges AS e WHERE e.node_a_id = n.id AND type LIKE '%follow%' ), follower_ids = (SELECT GROUP_CONCAT(e.node_a_id) FROM jos_anahita_edges AS e WHERE e.node_b_id = n.id AND type LIKE '%follow%' ),leader_ids = (SELECT GROUP_CONCAT(e.node_b_id) FROM jos_anahita_edges AS e WHERE e.node_a_id = n.id AND type LIKE '%follow%' ),blocker_ids = (SELECT GROUP_CONCAT(e.node_a_id) FROM jos_anahita_edges AS e WHERE e.node_b_id = n.id AND type LIKE '%block%' ),blocked_ids = (SELECT GROUP_CONCAT(e.node_b_id) FROM jos_anahita_edges AS e WHERE e.node_a_id = n.id AND type LIKE '% block%' ), administrator_ids = (SELECT GROUP_CONCAT(e.node_a_id) FROM jos_anahita_edges AS e WHERE e.node_b_id = n.id AND type LIKE '%administrator%' ),administrating_ids = (SELECT GROUP_CONCAT(e.node_b_id) FROM jos_anahita_edges AS e WHERE e.node_a_id = n.id AND type LIKE '%administrator%' )where type LIKE '%actor%';
UPDATE jos_anahita_nodes AS n SET subscriber_count = (SELECT count(e.node_a_id) FROM jos_anahita_edges AS e WHERE e.node_b_id = n.id AND type LIKE '%AnSeSubscription%' ), subscriber_ids = (SELECT GROUP_CONCAT(e.node_a_id) FROM jos_anahita_edges AS e WHERE e.node_b_id = n.id AND type LIKE '%AnSeSubscription%' );
UPDATE jos_anahita_nodes AS n SET mutual_count = (SELECT count(distinct e2.node_a_id) FROM jos_anahita_edges AS e1 INNER JOIN jos_anahita_edges AS e2 on e1.node_b_id = e2.node_a_id AND e2.type LIKE '%follow%' AND e2.node_b_id = e1.node_a_id WHERE e1.node_a_id = n.id AND e1.type LIKE '%follow%'),mutual_ids = (SELECT group_concat(distinct e2.node_a_id) FROM jos_anahita_edges AS e1 INNER JOIN jos_anahita_edges AS e2 on e1.node_b_id = e2.node_a_id AND e2.type LIKE '%follow%' AND e2.node_b_id = e1.node_a_id WHERE e1.node_a_id = n.id AND e1.type LIKE '%follow%') WHERE type LIKE '%person%';
UPDATE jos_anahita_nodes SET access = ( (CASE WHEN (@access := CAST((substr(permissions,locate('view:profile\":', permissions) + 14, 3)) AS DECIMAL(10,1)) )= 0 THEN 'public' WHEN @access = 1 THEN 'registered' WHEN @access = 2 THEN 'followers' WHEN @access = 3 THEN 'leaders' WHEN @access = 4 THEN 'mutuals' WHEN @access = 5 THEN 'admins' WHEN @access > 1 THEN 'special' ELSE 'no' END) ) WHERE permissions LIKE '%view:profile\":%';

//importing apps
DELETE FROM `jos_anahita_applications` WHERE component LIKE 'com_socialengine';
INSERT INTO jos_anahita_nodes (component, ordering, type) 
    SELECT app.component,app.id, '|AnSeNode|AnSeApp|lib.anahita.se.app' 
    FROM jos_anahita_applications AS app;

INSERT INTO jos_anahita_nodes (component, name, type) 
    SELECT DISTINCT IF(actortype.actor_type LIKE '%person%','com_people', CONCAT('com_',MID(actortype.actor_type, @first := (LOCATE('.', actortype.actor_type) + 1), LOCATE('.', actortype.actor_type, @first) - @first))) AS component,
    CASE actortype.actor_type            
            WHEN 'lib.anahita.se.app.actortype' THEN 'com.apps.domain.entity.actortype'
            WHEN 'lib.anahita.se.app'     THEN 'com.apps.domain.entity.app'
            WHEN 'lib.anahita.se.person'  THEN 'lib.anahita.se.entity.person'
            WHEN 'lib.anahita.se.story'   THEN 'com.stories.domain.entity.story'
            WHEN 'lib.anahita.se.comment' THEN 'lib.anahita.se.entity.comment'
            ELSE REPLACE(actortype.actor_type, '.model.','.domain.entity.')
        END AS name,
    '|AnSeEntityNode|ComAppsDomainEntityActortype|com.apps.domain.entity.actortype' AS type
    FROM jos_anahita_actor_application_access AS actortype;

INSERT INTO jos_anahita_edges(type,component,node_a_id,node_a_type,node_b_id,node_b_type,meta)
    SELECT '|AnSeEntityEdge|ComAppsDomainEntityAssignment|com.apps.domain.entity.assignment' AS type,
            app.component AS component,actortype.id AS node_a_id, 'com.apps.domain.entity.actortype' AS node_a_type,app.id AS node_b_id, 'lib.anahita.se.app' AS node_b_type, IF(app_access.access=0,1,app_access.access) AS meta 
    FROM jos_anahita_actor_application_access AS app_access 
    INNER JOIN jos_anahita_nodes AS app ON app.component = app_access.component AND app.type LIKE '|AnSeNode|AnSeApp|lib.anahita.se.app' 
    INNER JOIN jos_anahita_nodes AS actortype ON actortype.type LIKE '|AnSeEntityNode|ComAppsDomainEntityActortype|com.apps.domain.entity.actortype' AND actortype.name = CASE app_access.actor_type            
            WHEN 'lib.anahita.se.app.actortype' THEN 'com.apps.domain.entity.actortype'
            WHEN 'lib.anahita.se.app'     THEN 'com.apps.domain.entity.app'
            WHEN 'lib.anahita.se.person'  THEN 'lib.anahita.se.entity.person'
            WHEN 'lib.anahita.se.story'   THEN 'com.stories.domain.entity.story'
            WHEN 'lib.anahita.se.comment' THEN 'lib.anahita.se.entity.comment'
            ELSE REPLACE(app_access.actor_type, '.model.','.domain.entity.')
        END
    WHERE app_access.access IN (0,2);

INSERT INTO jos_anahita_edges(type,component,node_a_id,node_a_type,node_b_id,node_b_type)
    SELECT '|AnSeEntityEdge|ComAppsDomainEntityEnable|com.apps.domain.entity.enable',app.component,actor.id,REVERSE(MID(REVERSE(actor.type),1,LOCATE('|',REVERSE(actor.type))-1)),app.id,'lib.anahita.se.app' FROM jos_anahita_actor_application_relations AS relation 
    INNER JOIN jos_anahita_nodes AS app ON app.type LIKE '|AnSeNode|AnSeApp|lib.anahita.se.app' AND app.component = relation.component
    INNER JOIN jos_anahita_nodes AS actor ON actor.id = relation.actor_node_id
    WHERE added_to_profile = 1;
        
DROP TABLE jos_anahita_applications;    
DROP TABLE jos_anahita_actor_application_access;
DROP TABLE jos_anahita_actor_application_relations;
    
//importing comments     
CREATE INDEX story_comment_id ON jos_anahita_nodes (story_comment_id);
INSERT INTO jos_anahita_nodes(type,story_comment_id,component,body,parent_id,parent_type,created_by,created_on,modified_by,modified_on) 
    SELECT '|AnSeNode|AnSeComment|lib.anahita.se.comment',id, component, body,node_id,node_type,IF(created_by=0,NULL, created_by),IF(created_on='0000-00-00 00:00:00',NULL,created_on),NULL,NULL
    FROM jos_anahita_comments AS c;
    
UPDATE jos_anahita_nodes AS n, jos_anahita_nodes AS c SET n.story_comment_id = c.id 
    WHERE c.story_comment_id= n.story_comment_id AND 
          c.type LIKE '|AnSeNode|AnSeComment|lib.anahita.se.comment' AND 
          n.type LIKE '|AnSeNode|AnSeMedium|AnSeStory|lib.anahita.se.story' 
          AND n.story_comment_id IS NOT NULL;
          
UPDATE jos_anahita_nodes AS c SET story_comment_id = NULL 
    WHERE type LIKE '|AnSeNode|AnSeComment|lib.anahita.se.comment';
    
DROP TABLE jos_anahita_comments;
  
//create start date and end date index
CREATE INDEX start_date ON jos_anahita_nodes (start_date);
CREATE INDEX end_date ON jos_anahita_nodes (end_date);
CREATE INDEX start_date ON jos_anahita_edges (start_date);
CREATE INDEX end_date ON jos_anahita_edges (end_date);

//Node Table Updates
UPDATE jos_anahita_nodes SET
    name              = IF(name IN ('group_following','publicmessage','story_update','new_group','avatar_update') AND type LIKE '%Story%',REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(name, 'publicmessage','story_add'),'group_following','actor_follow'),'story_update','story_add'),'new_group','group_add'),'avatar_update','avatar_edit'),name),
    component         = IF(component LIKE 'com_socialengine', 'com_people',component),
    owner_id          = IF(owner_id         = 0, NULL, owner_id),
    last_comment_by   = IF(last_comment_by  = 0, NULL, last_comment_by),
    parent_id         = IF(parent_id        = 0, NULL, parent_id),
    created_by        = IF(created_by       = 0, NULL, created_by),
    modified_by       = IF(modified_by      = 0, NULL, modified_by),
    story_target_id   = IF(story_target_id  = 0, NULL, story_target_id),
    story_object_id   = IF(story_object_id  = 0, NULL, story_object_id),
    story_subject_id  = IF(story_subject_id = 0, NULL, story_subject_id),
    meta              = IF(meta = '',NULL,meta),
    start_date        = IF(start_date         = '0000-00-00 00:00:00', NULL, start_date),
    end_date          = IF(end_date           = '0000-00-00 00:00:00', NULL, end_date),    
    status_update_time    = IF(status_update_time  = '0000-00-00 00:00:00', NULL, status_update_time),
    last_comment_on       = IF(last_comment_on    = '0000-00-00 00:00:00', NULL, last_comment_on),
    modified_on           = IF(modified_on  = '0000-00-00 00:00:00', NULL, modified_on),
    created_on            = IF(created_on  = '0000-00-00 00:00:00', NULL, created_on),    
    person_lastvisitdate  = IF(person_lastvisitdate = '0000-00-00 00:00:00', NULL, person_lastvisitdate),
    access                = CASE access WHEN 5 THEN 'admins' WHEN 4 THEN 'mutuals' WHEN 3 THEN 'leaders' WHEN 2 THEN 'followers' WHEN 1 THEN 'registered' WHEN 0 THEN 'public' ELSE access END,
    enabled               = IF(type LIKE '%Person%',1,enabled),
    permissions           = IF(permissions = '', NULL, REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(permissions,':view:',':access:'),'com_socialengine','com_people'),'.1',''),'.',''),'0','"public"'),'1','"registered"'),'2','"followers"'),'3','"leaders"'),'4','"mutuals"'),'5','"admins"')),
    filename              = IF(type LIKE '%Person%' AND filename <> '', concat_ws('/','com_socialengine', filename), filename),
    parent_type           = IF(parent_type = '', NULL, 
        CASE parent_type
            WHEN 'lib.anahita.se.app.actortype' THEN 'com.apps.domain.entity.actortype'
            WHEN 'lib.anahita.se.app'     THEN 'com.apps.domain.entity.app'
            WHEN 'lib.anahita.se.person'  THEN 'lib.anahita.se.entity.person'
            WHEN 'lib.anahita.se.story'   THEN 'com.stories.domain.entity.story'
            WHEN 'lib.anahita.se.comment' THEN 'lib.anahita.se.entity.comment' 
            ELSE REPLACE(REPLACE(parent_type, '.model.','.domain.entity.'), 'site::','')
        END
    ),    
    story_object_type = IF(story_object_type  = '', NULL,  
        CASE story_object_type
            WHEN 'lib.anahita.se.app.actortype' THEN 'com.apps.domain.entity.actortype'
            WHEN 'lib.anahita.se.app'     THEN 'com.apps.domain.entity.app'
            WHEN 'lib.anahita.se.person'  THEN 'lib.anahita.se.entity.person'
            WHEN 'lib.anahita.se.story'   THEN 'com.stories.domain.entity.story'
            WHEN 'lib.anahita.se.comment' THEN 'lib.anahita.se.entity.comment' 
            ELSE REPLACE(REPLACE(story_object_type, '.model.','.domain.entity.'), 'site::','')
        END
    ),
    owner_type        = IF(owner_type         = '', NULL,  
        CASE owner_type
            WHEN 'lib.anahita.se.app.actortype' THEN 'com.apps.domain.entity.actortype'
            WHEN 'lib.anahita.se.app'     THEN 'com.apps.domain.entity.app'
            WHEN 'lib.anahita.se.person'  THEN 'lib.anahita.se.entity.person'
            WHEN 'lib.anahita.se.story'   THEN 'com.stories.domain.entity.story'
            WHEN 'lib.anahita.se.comment' THEN 'lib.anahita.se.entity.comment' 
            ELSE REPLACE(REPLACE(owner_type, '.model.','.domain.entity.'), 'site::','')
        END
    ),
    
    type = REPLACE(MID(REPLACE(REPLACE(REPLACE(
        CASE 
            WHEN type LIKE '|AnSeNode|AnSeActor|AnSePerson|lib.anahita.se.person'      THEN '|AnSeEntityNode|AnSeEntityActor|AnSeEntityPerson|lib.anahita.se.entity.person'
            WHEN type LIKE '|AnSeNode|AnSeAppActortype|lib.anahita.se.app.actortype'   THEN '|AnSeEntityNode|AnSeEntityActortype|com.apps.domain.entity.actortype'
            WHEN type LIKE '|AnSeNode|AnSeApp|lib.anahita.se.app'  THEN '|AnSeEntityNode|ComAppsDomainEntityApp|com.apps.domain.entity.app'
            WHEN type LIKE '|AnSeNode|AnSeComment|lib.anahita.se.comment' THEN '|AnSeEntityNode|AnSeEntityComment|lib.anahita.se.entity.comment'
            WHEN type LIKE '|AnSeNode|AnSeMedium|AnSeStory|lib.anahita.se.story'   THEN '|AnSeEntityNode|AnSeEntityMedium|ComStoriesDomainEntityStory|com.stories.domain.entity.story'          
            WHEN type LIKE '|AnSeNode|AnSeMedium|%' THEN REPLACE(type, '|AnSeNode|AnSeMedium|','|AnSeEntityNode|AnSeEntityMedium|')
            WHEN type LIKE '|AnSeNode|AnSeActor|AnSeGroup|%' THEN REPLACE(type, '|AnSeNode|AnSeActor|AnSeGroup|', '|AnSeEntityNode|AnSeEntityActor|AnSeEntityGroup|')
            WHEN type LIKE '|AnSeNode|AnSeNotification|lib.anahita.se.notification' THEN '|AnSeEntityNode|ComNotificationsDomainEntityNotification|com.notifications.domain.entity.notification'
            WHEN type LIKE '|AnSeNode|lib.anahita.se.node' THEN '|AnSeEntityNode|lib.anahita.se.entity.node'
            WHEN type LIKE '|AnSeNode|%' THEN REPLACE(type,'|AnSeNode|','|AnSeEntityNode|')
            ELSE type  
        END,
        '.model.','.domain.entity.'),'Model', 'DomainEntity'),'site::',''), locate('|',type)+1),'|',',')
    ;

CREATE INDEX type_enabled ON jos_anahita_nodes (type,enabled);
CREATE INDEX type_modifed_on ON jos_anahita_nodes (type,modified_on);
CREATE INDEX type_created_on ON jos_anahita_nodes (type,created_on);
CREATE INDEX type_status_update_time ON jos_anahita_nodes (type, status_update_time); 

UPDATE jos_anahita_edges SET 
    node_a_type  = IF(node_a_type  = '', NULL, 
        CASE node_a_type
            WHEN 'lib.anahita.se.app.actortype' THEN 'com.apps.domain.entity.actortype'
            WHEN 'lib.anahita.se.app'     THEN 'com.apps.domain.entity.app'
            WHEN 'lib.anahita.se.person'  THEN 'lib.anahita.se.entity.person'
            WHEN 'lib.anahita.se.story'   THEN 'com.stories.domain.entity.story'
            WHEN 'lib.anahita.se.comment' THEN 'lib.anahita.se.entity.comment' 
            ELSE REPLACE(REPLACE(node_a_type, '.model.','.domain.entity.'), 'site::','')
        END
    ),
    node_b_type  = IF(node_b_type  = '', NULL, 
        CASE node_b_type            
            WHEN 'lib.anahita.se.app.actortype' THEN 'com.apps.domain.entity.actortype'
            WHEN 'lib.anahita.se.app'     THEN 'com.apps.domain.entity.app'
            WHEN 'lib.anahita.se.person'  THEN 'lib.anahita.se.entity.person'
            WHEN 'lib.anahita.se.story'   THEN 'com.stories.domain.entity.story'
            WHEN 'lib.anahita.se.comment' THEN 'lib.anahita.se.entity.comment'
            ELSE REPLACE(REPLACE(node_b_type, '.model.','.domain.entity.'), 'site::','')
        END
    ),
    type = REPLACE(MID(REPLACE(REPLACE(REPLACE(
        CASE 
            WHEN type LIKE '|AnSeEdge|AnSeGraph|AnSeGraphFollow|lib.anahita.se.graph.follow' THEN '|AnSeEntityEdge|AnSeEntityFollow|lib.anahita.se.entity.follow'
            WHEN type LIKE '|AnSeEdge|AnSeGraph|AnSeGraphBlock|lib.anahita.se.graph.block' THEN '|AnSeEntityEdge|AnSeEntityBlock|lib.anahita.se.entity.block'
            WHEN type LIKE '|AnSeEdge|AnSeAdministratorEdge|lib.anahita.se.administrator.edge' THEN '|AnSeEntityEdge|AnSeEntityAdministrator|lib.anahita.se.entity.administrator'
            WHEN type LIKE '|AnSeEdge|AnSeSubscription|lib.anahita.se.subscription' THEN '|AnSeEntityEdge|AnSeEntitySubscription|lib.anahita.se.entity.subscription'
            WHEN type LIKE '|AnSeEdge|AnSeOwnership|lib.anahita.se.ownership' THEN '|AnSeEntityEdge|AnSeEntityOwnership|lib.anahita.se.entity.ownership'
            WHEN type LIKE '|AnSeEdge|AnSeVote|AnSeVoteUp|lib.anahita.se.vote.up' THEN '|AnSeEntityEdge|AnSeEntityVote|AnSeEntityVoteup|lib.anahita.se.entity.voteup'
            WHEN type LIKE '|AnSeEdge|AnSeAppInstall|lib.anahita.se.app.install' THEN '|AnSeEntityEdge|ComAppsDomainEntityEnable|com.apps.domain.entity.enable'
            WHEN type LIKE '|AnSeEdge|AnSeAppAssignment|lib.anahita.se.app.assignment' THEN '|AnSeEntityEdge|ComAppsDomainEntityAssignment|com.apps.domain.entity.assignment'
            WHEN type LIKE '|AnSeEdge|%' THEN REPLACE(type,'|AnSeEdge|','|AnSeEntityEdge|')
            ELSE type 
        END,
        '.model.','.domain.entity.'),'Model', 'DomainEntity'),'site::',''), locate('|',type)+1),'|',',')
;

           
DROP TABLE IF EXISTS last_comments;        
CREATE TABLE last_comments AS (SELECT parent_id AS node_id, MAX(comment.created_on) AS last_comment_time, MAX(comment.id) last_comment_id, count(comment.id) AS comment_count, (SELECT created_by FROM jos_anahita_nodes WHERE MAX(comment.id) = id) AS last_commentor FROM jos_anahita_nodes AS comment WHERE comment.type LIKE '%AnSeEntityComment%' GROUP BY comment.parent_id);
UPDATE jos_anahita_nodes AS node INNER JOIN last_comments AS comment ON node.id = comment.node_id  SET node.last_comment_by = comment.last_commentor, node.last_comment_id = comment.last_comment_id, node.comment_count = comment.comment_count, node.last_comment_on = comment.last_comment_time;
DROP TABLE IF EXISTS last_comments;

DROP TABLE IF EXISTS `jos_opensocial_profiles`;
RENAME TABLE jos_anahita_people_profiles TO jos_opensocial_profiles;

 
INSERT INTO `jos_plugins` VALUES 
    (NULL, 'Content Filter - Hyperlink', 'link', 'contentfilter', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
    (NULL, 'Content Filter - Syntax', 'syntax', 'contentfilter', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
    (NULL, 'Content Filter - Video', 'video', 'contentfilter', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
    (NULL, 'Content Filter - P Tag', 'ptag', 'contentfilter', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),    
    (NULL, 'Storage - Local', 'local',  'storage', 0, 0, (SELECT IF(params NOT LIKE '%storage=amazon%',1,0) FROM jos_components WHERE `option` LIKE 'com_socialengine')
    , 1, 0, 0, '0000-00-00 00:00:00', ''),
    (NULL, 'Storage - Amazon S3', 's3', 'storage', 0, 1, (SELECT IF(params LIKE '%storage=amazon%',1,0) FROM jos_components WHERE `option` LIKE 'com_socialengine')
    , 0, 0, 0, '0000-00-00 00:00:00', ((SELECT IF(params LIKE '%storage=amazon%',MID(params,LOCATE('bucket', params)),'') FROM jos_components WHERE `option` LIKE 'com_socialengine')))
;

INSERT INTO `jos_components` VALUES
    (NULL, 'Apps', 'option=com_apps', 0, 0, 'option=com_apps', 'Apps', 'com_apps', 0, 'js/ThemeOffice/component.png', 1, '', 1),
    (NULL, 'Dashboard', 'option=com_dashboard', 0, 0, '', '', 'com_dashboard', 0, '', 1, '', 1),
    (NULL, 'People', 'option=com_people', 0, 0, '', '', 'com_people', 0, '', 1, '', 1),    
    (NULL, 'Stories', 'option=com_stories', 0, 0, '', '', 'com_stories', 0, '', 1, '', 1),
    (NULL, 'Notifications', 'option=com_notifications', 0, 0, 'option=com_notifications', 'Notifications', 'com_notifications', 0, 'js/ThemeOffice/component.png', 1, '', 1)    
    ;

DELETE FROM `jos_plugins` WHERE 
    (`folder` LIKE 'authentication' AND `element` IN ('ldap','gmail','openid')) OR 
    (`folder` LIKE 'xmlrpc') OR
    (`folder` LIKE 'roknavmenu') OR
    (`folder` LIKE 'socialshare') OR
    (`element` LIKE 'tagmeta') OR
    (`folder` LIKE 'content' AND `element` IN ('vote','geshi', 'jw_allvideos', 'slideshare15')) OR
    (`folder` LIKE 'search' AND `element` IN ('contacts','newsfeeds', 'weblinks')) OR
    (`folder` LIKE 'system'  AND `element` IN ('mtupgrade','roktracking', 'backlink', 'rokbox'))
    (`folder` LIKE 'editors-xtd'  AND `element` IN ('image'))    
    ;
    
DELETE FROM `jos_components` WHERE `option` IN ( 
   'com_socialengine', 'com_banners', 'com_roknavmenubundle', 'com_massmail', 
   'com_messages', 'com_banners', 'com_weblinks', 'com_contact', 'com_poll', 'com_newsfeeds',
   'com_wrapper', 'com_massmail', 'com_messages', 'com_rokmodule', 'com_media', 'com_tagmeta',
   'com_invites'
);  
  
DELETE FROM `jos_modules` WHERE 
    `module` IN ('mod_randomimage','mod_stats','mod_archive','mod_mostread','mod_rokadminaudit','mod_quickicon','mod_rokuserstats','mod_rokuserchart', 'mod_rokstories');

UPDATE `jos_modules` SET `content` = REPLACE(`content`, 'link-button', 'btn primary') WHERE `module` = 'mod_custom';
UPDATE `jos_modules` SET `content` = REPLACE(`content`, 'rounded', '') WHERE `module` = 'mod_custom';

UPDATE `jos_menu` SET 
    link = IF(link LIKE '%view=dashboard%',REPLACE(link,'com_socialengine','com_dashboard'), REPLACE(link,'com_socialengine','com_people'))
    WHERE link LIKE '%com_socialengine%';

UPDATE `jos_menu` SET
    `componentid` = IF(`link` LIKE '%com_dashboard%', (SELECT id FROM `jos_components` WHERE `option` LIKE 'com_dashboard' AND parent = ''  LIMIT 1), (SELECT id FROM `jos_components` WHERE `option` LIKE 'com_people' AND parent = '' LIMIT 1))
    WHERE `link` LIKE '%com_dashboard%' OR `link` LIKE '%com_people%';
        
UPDATE `jos_menu` SET link = REPLACE(link, 'index.php?option=com_groups&view=groups&layout=following&oid=viewer&filter=following', 'index.php?option=com_groups&view=groups&oid=viewer&filter=following');
UPDATE `jos_menu` SET link = REPLACE(link, 'index.php?option=com_groups&view=groups&layout=administering&oid=viewer&filter=following', 'index.php?option=com_groups&view=groups&oid=viewer&filter=administering');
UPDATE `jos_menu` SET link = REPLACE(link, 'index.php?option=com_groups&view=groups&layout=leaders&oid=viewer&filter=following', 'index.php?option=com_groups&view=groups&oid=viewer&filter=leaders');    
UPDATE `jos_menu` SET link = REPLACE(link, 'index.php?option=com_people&view=socialgraph', 'index.php?option=com_people&view=person&id=5&get=graph'); 

UPDATE `jos_menu` SET link = REPLACE(link, 'index.php?option=com_subscriptions&view=subscriptions', 'index.php?option=com_subscriptions&view=subscription');

DELETE FROM `jos_menu` WHERE link LIKE '%com_invites%';
    
DROP TABLE IF EXISTS jos_banner;
DROP TABLE IF EXISTS jos_bannerclient;
DROP TABLE IF EXISTS jos_bannertrack;

DROP TABLE IF EXISTS jos_messages;
DROP TABLE IF EXISTS jos_messages_cfg;

DROP TABLE IF EXISTS jos_poll_data;
DROP TABLE IF EXISTS jos_poll_date;
DROP TABLE IF EXISTS jos_poll_menu;
DROP TABLE IF EXISTS jos_polls;

DROP TABLE IF EXISTS jos_rokadminaudit;
DROP TABLE IF EXISTS jos_rokuserstats;

DROP TABLE IF EXISTS jos_weblinks;

DROP TABLE IF EXISTS jos_newsfeeds;
DROP TABLE IF EXISTS jos_contact_details;
DROP TABLE IF EXISTS jos_content_rating;

DROP TABLE IF EXISTS jos_tagmeta;

UPDATE `jos_modules` SET `position` = REPLACE(`position`, 'content-bottom', 'maintop') WHERE `position` LIKE '%content-bottom%';

UPDATE `jos_modules` SET `params` = 'title-1=Bazaar\nlink-1=index.php?option=com_bazaar\nicon-1=anahita.png\ntitle-2=Social Apps\nlink-2=index.php?option=com_apps\nicon-2=application_view_icons.png\ntitle-3=Plugins\nlink-3=index.php?option=com_plugins\nicon-3=brick.png\ntitle-4=Modules\nlink-4=index.php?option=com_modules\nicon-4=brick.png\ntitle-5=Templates\nlink-5=index.php?option=com_templates\nicon-5=color_management.png\ntitle-6=Extend\nlink-6=index.php?option=com_installer\nicon-6=package.png\ntitle-7=Configuration\nlink-7=index.php?option=com_config\nicon-7=cog.png\nquickfields=[{"icon":"anahita.png","link":"index.php?option=com_bazaar","title":"Bazaar"},{"icon":"application_view_icons.png","link":"index.php?option=com_apps","title":"Social Apps"},{"icon":"brick.png","link":"index.php?option=com_plugins","title":"Plugins"},{"icon":"brick.png","link":"index.php?option=com_modules","title":"Modules"},{"icon":"color_management.png","link":"index.php?option=com_templates","title":"Templates"},{"icon":"package.png","link":"index.php?option=com_installer","title":"Extend"},{"icon":"cog.png","link":"index.php?option=com_config","title":"Configuration"}]\n\n' WHERE `module` LIKE 'mod_rokquicklinks';

INSERT INTO `jos_plugins` VALUES (NULL, 'Installer - Core', 'core', 'installer', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', '');
