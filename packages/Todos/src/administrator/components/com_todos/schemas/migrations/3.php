<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @package    Com_Todos
 * @subpackage Schema_Migration
 */

/**
 * Schema Migration
 *
 * @package    Com_Todos
 * @subpackage Schema_Migration
 */
class ComTodosSchemaMigration3 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        $timeThen = microtime(true);    
            
        //change todo formats from html to string    
        $todos = dbfetch('SELECT id FROM #__anahita_nodes WHERE type LIKE "%com:todos.domain.entity.todo" ');
    
        foreach($todos as $todo)
        {
            $id = $todo['id'];   
            $entity = KService::get('com://site/todos.domain.entity.todo')
            ->getRepository()->getQuery()->disableChain()->fetch($id)->save();  

            //dboutput( $id.', ' );        
        }
        
        $timeDiff = microtime(true) - $timeThen;
        dboutput("TIME: ($timeDiff)"."\n");
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}