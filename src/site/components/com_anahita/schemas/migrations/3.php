<?php

/** 
 * LICENSE: ##LICENSE##.
 */

/**
 * Schema Migration.
 */
class ComAnahitaSchemaMigration3 extends ComMigratorMigrationVersion
{
    /**
     * Called when migrating up.
     */
    public function up()
    {
        //add html component if not already in
        if (!dbexists('SELECT id FROM #__components WHERE `option` = "com_pages"')) {
            $this[] = "INSERT INTO `#__components` VALUES(NULL, 'Html', 'option=com_pages', 0, 0, 'option=com_pages', 'Html', 'com_pages', 0, 'js/ThemeOffice/component.png', 1, '', 1)";
        }
        $this[] = "UPDATE #__components SET admin_menu_link='option=com_pages', admin_menu_alt='Html', admin_menu_img='js/ThemeOffice/component.png' WHERE `option` = 'com_pages'";
        $this[] = "DELETE FROM #__plugins WHERE folder IN ('content','editors','editors-xtd') OR element = 'mtupgrade'";
    }

    /**
     * Called when rolling back a migration.
     */
    public function down()
    {
        //add your migration here        
    }
}
