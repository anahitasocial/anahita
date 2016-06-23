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
class ComAnahitaSchemaMigration15 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
      //Create new fields for com_locations
      dbexec("ALTER TABLE `#__nodes` ADD `geo_address` VARCHAR(255) DEFAULT NULL AFTER `geo_longitude`");
      dbexec("ALTER TABLE `#__nodes` ADD `geo_city` VARCHAR(50) DEFAULT NULL AFTER `geo_address`");
      dbexec("ALTER TABLE `#__nodes` ADD `geo_state_province` VARCHAR(50) DEFAULT NULL AFTER `geo_city`");
      dbexec("ALTER TABLE `#__nodes` ADD `geo_country` VARCHAR(30) DEFAULT NULL AFTER `geo_state_province`");
      dbexec("ALTER TABLE `#__nodes` ADD `geo_postalcode` VARCHAR(15) DEFAULT NULL AFTER `geo_country`");

      //add extension records
      dbexec("INSERT INTO `#__components` (`name`, `link`, `admin_menu_link`, `admin_menu_alt`, `admin_menu_img`, `option`,`iscore`, `enabled`) VALUES ('Locations', 'option=com_locations', 'option=com_locations', 'Locations', 'js/ThemeOffice/component.png', 'com_locations', 1, 1)");

    }

   /**
    * Called when rolling back a migration
    */
    public function down()
    {
        //add your migration here
    }
}
