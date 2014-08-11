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
class ComAnahitaSchemaMigration6 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {    
        //add the mention tag contentfilter
        dbexec('INSERT INTO #__plugins (name,element,folder,iscore,published) VALUES (\'Mention\', \'mention\',\'contentfilter\',1,1)');
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}