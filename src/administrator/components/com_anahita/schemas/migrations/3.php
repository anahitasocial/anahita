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
class ComAnahitaSchemaMigration3 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        //add html component if not already in
        if ( !dbexists('SELECT id FROM #__components WHERE `option` = "com_html"') ) {
            $this[] = "INSERT INTO `#__components` VALUES(NULL, 'Html', 'option=com_html', 0, 0, 'option=com_html', 'Html', 'com_html', 0, 'js/ThemeOffice/component.png', 1, '', 1)";
        }
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}