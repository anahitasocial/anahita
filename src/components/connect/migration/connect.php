<?php 

function connect_1()
{
    $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `#__anahita_oauths` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `component` varchar(255) NOT NULL,
  `owner_id` bigint(11) NOT NULL,
  `owner_type` varchar(100) NOT NULL,
  `access_token` varchar(100) NOT NULL,
  `access_token_secret` varchar(100) NOT NULL,
  `service_id` varchar(100) NOT NULL,
  `update_status` tinyint(1) NOT NULL DEFAULT '0',
  `profile_id` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `owner_id_and_profile_id_and_service_id` (`owner_id`,`profile_id`,`service_id`)
) ENGINE=InnoDB CHARACTER SET `utf8` COLLATE `utf8_general_ci`;        
EOF;
    dbexec($sql);
    dbexec('RENAME TABLE #__anahita_oauths TO #__connect_sessions');
    dbexec('alter table #__connect_sessions change  access_token token_key text null');
    dbexec('alter table #__connect_sessions change  access_token_secret token_secret text null');
    dbexec('alter table #__connect_sessions change  service_id api varchar(100) not null');
    dbexec('alter table #__connect_sessions drop column type');
    dbexec("UPDATE jos_connect_sessions SET owner_type = REPLACE(REPLACE(REPLACE(owner_type,'lib.anahita.se.person','lib.anahita.se.entity.person'),'site::',''),'.model.','.domain.entity.')");
}

function connect_2()
{
    dbexec("UPDATE jos_connect_sessions SET owner_type = REPLACE(REPLACE(owner_type,'lib.anahita.se.entity.person','com:people.domain.entity.person'),'com.','com:')");
}

?>