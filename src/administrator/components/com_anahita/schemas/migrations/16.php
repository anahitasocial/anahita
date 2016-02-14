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
class ComAnahitaSchemaMigration16 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        dbexec('DELETE FROM `#__components` WHERE `option` = \'com_pages\' ');
        dbexec('UPDATE `#__components` SET `name` = \'Pages\', `link` = \'option=com_pages\', `admin_menu_link` = \'option=com_pages\', `admin_menu_alt` = \'Pages\', `option` = \'com_pages\', `ordering` = 0 WHERE `option` = \'com_html\' ');
    }

   /**
    * Called when rolling back a migration
    */
    public function down()
    {
        //add your migration here
    }
}
