<?php 

function photos_1()
{
    dbexec("UPDATE jos_anahita_nodes SET `name` = 'photo_add' WHERE `component` LIKE 'com_photos' AND `name` LIKE 'new_photo'");
    dbexec("UPDATE jos_anahita_nodes SET `name` = 'album_add' WHERE `component` LIKE 'com_photos' AND `name` LIKE 'new_album'");    
}

function photos_2()
{   
    dbexec("UPDATE jos_anahita_nodes SET mimetype = NULL, filename = IF(excerpt IS NULL OR excerpt = '',IF(filename <> '' and filename NOT LIKE '%.jpg%',CONCAT(filename,'.jpg'), filename),IF(excerpt NOT LIKE '%.jpg%' AND excerpt <> '',CONCAT(excerpt,'.jpg'), excerpt)), excerpt = NULL WHERE type LIKE 'ComMediumDomainEntityMedium,ComPhotosDomainEntityAlbum,com:photos.domain.entity.album'");
    dbexec("UPDATE jos_anahita_nodes SET mimetype = NULL, filename = IF(excerpt <> '' AND (filename IS NULL or filename = ''),excerpt,filename), excerpt = NULL WHERE type LIKE 'ComMediumDomainEntityMedium,ComPhotosDomainEntityPhoto,com:photos.domain.entity.photo'");
    dbexec("UPDATE jos_anahita_nodes SET filename = IF(filename <> '' and filename NOT LIKE '%.jpg%',CONCAT(filename,'.jpg'), filename) WHERE type LIKE 'ComMediumDomainEntityMedium,ComPhotosDomainEntityPhoto,com:photos.domain.entity.photo'");    
}

function photos_3()
{d
	dbexec("UPDATE jos_anahita_nodes SET `type` = 'ComMediumDomainEntityMedium,ComPhotosDomainEntitySet,com:photos.domain.entity.set' WHERE `component` LIKE 'com_photos' AND type LIKE '%com:photos.domain.entity.album%'");
	
	dbexec("UPDATE jos_anahita_nodes SET `name` = 'set_add' WHERE `component` LIKE 'com_photos' AND `name` LIKE 'album_add'");
	dbexec("UPDATE jos_anahita_nodes SET `alias` = 'set_add' WHERE `component` LIKE 'com_photos' AND `alias` LIKE 'album_add'");
	
	dbexec("UPDATE jos_anahita_nodes SET `name` = 'set_comment' WHERE `component` LIKE 'com_photos' AND `name` LIKE 'album_comment'");
	dbexec("UPDATE jos_anahita_nodes SET `alias` = 'set_comment' WHERE `component` LIKE 'com_photos' AND `alias` LIKE 'album_comment'");
	
	dbexec("UPDATE jos_anahita_nodes SET `story_object_type` = 'com:photos.domain.entity.set' WHERE `component` LIKE 'com_photos' AND `story_object_type` LIKE 'com:photos.domain.entity.album'");
	
	dbexec("UPDATE jos_anahita_edges SET `node_b_type` = 'com:photos.domain.entity.set' WHERE `node_b_type` LIKE 'com:photos.domain.entity.album'");
	
	dbexec("UPDATE jos_anahita_nodes SET `parent_type` = 'com:photos.domain.entity.set' WHERE parent_type = 'com:photos.domain.entity.album' AND component = 'com_photos' AND type = 'ComBaseDomainEntityComment,com:photos.domain.entity.comment' ");

	dbexec("UPDATE jos_content SET `introtext` = REPLACE(`introtext`, 'album_id=', 'set_id=')");
	dbexec("UPDATE jos_content SET `fulltext` = REPLACE(`fulltext`, 'album_id=', 'set_id=')");
}