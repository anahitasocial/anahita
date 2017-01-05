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
class ComAnahitaSchemaMigration19 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        //legacy app clean up
        dbexec('DELETE FROM `#__migrator_versions` WHERE component = "opensocial"');

        dbexec('ALTER TABLE `#__nodes` CHANGE `meta` `meta` text DEFAULT NULL');
        dbexec('ALTER TABLE `#__edges` CHANGE `meta` `meta` text DEFAULT NULL');
        dbexec('ALTER TABLE `#__components` CHANGE `params` `meta` text DEFAULT NULL');
        dbexec('ALTER TABLE `#__components` DROP COLUMN `link`');
        dbexec('ALTER TABLE `#__components` DROP COLUMN `menuid`');
        dbexec('ALTER TABLE `#__components` DROP COLUMN `admin_menu_link`');
        dbexec('ALTER TABLE `#__components` DROP COLUMN `admin_menu_alt`');
        dbexec('ALTER TABLE `#__components` DROP COLUMN `admin_menu_img`');

        //deleting legacy data
        dbexec('DELETE FROM `#__components` WHERE `option` IN ("com_config","com_cpanel","com_plugins","com_templates","com_components", "com_languages")');

        $this->_updateMeta('components');

        dbexec('UPDATE `#__components` SET meta = REPLACE(meta, \'"allowUserRegistration":\', \'"allow_registration":\') WHERE `option` = \'com_people\'');

        //legacy data clean up
        dbexec('DELETE FROM `#__plugins` WHERE `element` = "invite" ');
        dbexec('ALTER TABLE `#__plugins` DROP COLUMN `access`');
        dbexec('ALTER TABLE `#__plugins` DROP COLUMN `client_id`');
        dbexec('ALTER TABLE `#__plugins` DROP COLUMN `checked_out`');
        dbexec('ALTER TABLE `#__plugins` DROP COLUMN `checked_out_time`');

        $this->_updateMeta('plugins');

        dbexec('DROP TABLE IF EXISTS `#__templates_menu`');
        dbexec('DROP TABLE IF EXISTS `#__session`');
        dbexec('DROP TABLE IF EXISTS `#__sessions`');

        $query = "CREATE TABLE `#__sessions` ("
        ."`id` SERIAL,"
        ."`session_id` char(64) NOT NULL,"
        ."`node_id` bigint(11) NOT NULL DEFAULT 0,"
        ."`username` varchar(255) DEFAULT NULL,"
        ."`usertype` varchar(255),"
        ."`time` INT(11) DEFAULT 0,"
        ."`guest` tinyint(2) DEFAULT '1',"
        ."`meta` longtext,"
        ."PRIMARY KEY (`id`),"
        ."KEY `whosonline` (`guest`,`usertype`,`username`),"
        ."UNIQUE KEY `session_id` (`session_id`),"
        ."KEY `node_id` (`node_id`),"
        ."KEY `username` (`username`)"
        .") ENGINE=InnoDB CHARACTER SET=utf8";
        dbexec($query);

        //for people the alias is the same as username
        dbexec("UPDATE `#__nodes` SET alias = person_username WHERE type LIKE '%com:people.domain.entity.person' ");

        dbexec('DROP TABLE IF EXISTS `#__people_people`');

        $query = "CREATE TABLE `#__people_people` ("
        ."`people_person_id` SERIAL,"
        ."`node_id` BIGINT UNSIGNED NOT NULL,"
        ."`userid` INT(11) DEFAULT NULL,"
        ."`email` varchar(255) DEFAULT NULL,"
        ."`username` varchar(255) DEFAULT NULL,"
        ."`password` varchar(255) DEFAULT NULL,"
        ."`usertype` varchar(50) DEFAULT NULL,"
        ."`gender` varchar(50) DEFAULT NULL,"
        ."`given_name` varchar(255) DEFAULT NULL,"
        ."`family_name` varchar(255) DEFAULT NULL,"
        ."`network_presence` tinyint(3) NOT NULL DEFAULT 0,"
        ."`last_visit_date` datetime DEFAULT NULL,"
        ."`time_zone` int(11) DEFAULT NULL,"
        ."`language` varchar(100) DEFAULT NULL,"
        ."`activation_code` varchar(255) DEFAULT NULL,"
        ."PRIMARY KEY (`people_person_id`),"
        ."KEY `usertype` (`usertype`),"
        ."UNIQUE KEY `username` (`username`),"
        ."UNIQUE KEY `email` (`email`),"
        ."UNIQUE KEY `node_id` (`node_id`),"
        ."KEY `last_visit_date` (`last_visit_date`)"
        .") ENGINE=InnoDB CHARACTER SET=utf8";
        dbexec($query);

        $query = "INSERT INTO `#__people_people` ("
        ."`node_id`,`userid`,`username`,`usertype`,`gender`,"
        ."`email`,`given_name`,`family_name`,"
        ."`last_visit_date`,`time_zone`,`language` )"
        ." SELECT "
        ."`id`,`person_userid`,`person_username`,`person_usertype`,`actor_gender`,"
        ."`person_useremail`,`person_given_name`,`person_family_name`,"
        ."`person_lastvisitdate`,`person_time_zone`,`person_language` "
        ." FROM `#__nodes`"
        ." WHERE `type` LIKE '%com:people.domain.entity.person'"
        ." ORDER BY `id`";
        dbexec($query);

        $query = "UPDATE `#__people_people` AS `p` "
        ."INNER JOIN `#__users` AS `u` ON `u`.`id` = `p`.`userid`"
        ." SET "
        ."`p`.`password` = `u`.`password`,"
        ."`p`.`last_visit_date` = `u`.`lastvisitDate`,"
        ."`p`.`activation_code` = `u`.`activation`";
        dbexec($query);

        dbexec('ALTER TABLE `#__nodes` DROP COLUMN `person_userid`');
        dbexec('ALTER TABLE `#__nodes` DROP COLUMN `person_username`');
        dbexec('ALTER TABLE `#__nodes` DROP COLUMN `person_useremail`');
        dbexec('ALTER TABLE `#__nodes` DROP COLUMN `person_usertype`');
        dbexec('ALTER TABLE `#__nodes` DROP COLUMN `person_lastvisitdate`');
        dbexec('ALTER TABLE `#__nodes` DROP COLUMN `actor_gender`');
        dbexec('ALTER TABLE `#__nodes` DROP COLUMN `person_given_name`');
        dbexec('ALTER TABLE `#__nodes` DROP COLUMN `person_family_name`');
        dbexec('ALTER TABLE `#__nodes` DROP COLUMN `person_network_presence`');

        dbexec('ALTER TABLE `#__nodes` CHANGE `person_time_zone` `timezone` int(3) DEFAULT NULL');
        dbexec('ALTER TABLE `#__nodes` CHANGE `person_language` `language` VARCHAR(50) DEFAULT NULL');

        dbexec('ALTER TABLE `#__people_people` DROP COLUMN `userid`');

        dbexec('DROP TABLE IF EXISTS `#__users`');

        //update user and authentication plugins
        dbexec("UPDATE `#__plugins` SET `element` = 'anahita' WHERE `element` = 'joomla'");
        dbexec("UPDATE `#__plugins` SET `name` = 'User - Anahita' WHERE `folder` = 'user' AND `element` = 'anahita'");

        dbexec('ALTER TABLE `#__nodes` CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4');
        dbexec('ALTER TABLE `#__nodes` CHANGE alias alias VARCHAR(255) CHARACTER SET utf8mb4');
        dbexec('ALTER TABLE `#__nodes` CHANGE body body MEDIUMTEXT CHARACTER SET utf8mb4');
        dbexec('ALTER TABLE `#__nodes` CHANGE excerpt excerpt TEXT CHARACTER SET utf8mb4');
    }

   /**
    * Called when rolling back a migration
    */
    public function down()
    {
        //add your migration here
    }

    protected function _updateMeta($type)
    {
        $rows = dbfetch('SELECT `id`,`meta` FROM `#__'.$type.'`');

        foreach($rows as $row) {

            $meta = $row['meta'];

            if ($meta != '') {
                $json = array();
                $lines = explode("\n", $meta);

                foreach ($lines as $line) {

                    $line = explode('=', $line, 2);
                    $key = $line[0];

                    if (isset($line[1])) {
                          $value = $line[1];
                          $json[$key] = htmlentities($value, ENT_QUOTES, "UTF-8");
                    }
                }

                if (count($json)) {
                    $json = json_encode($json);
                    dbexec('UPDATE `#__'.$type.'` SET `meta` = \''.$json.'\' WHERE `id` = '.$row['id']);
                }
            }
        }
    }
}
