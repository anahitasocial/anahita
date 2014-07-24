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
class ComTodosSchemaMigration2 extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        $timeThen = microtime(true);
        
        dbexec('ALTER TABLE #__todos_milestones CHANGE todolists_count todos_count BIGINT(11) NULL DEFAULT NULL');
        dbexec('ALTER TABLE #__todos_milestones ADD COLUMN open_todos_count BIGINT(11) NULL DEFAULT NULL AFTER todos_count');
        
       	$todolists = dbfetch('SELECT `id`, `parent_id`, `alias` FROM #__anahita_nodes WHERE `type` LIKE \'%com:todos.domain.entity.todolist\' ');
        	
        foreach($todolists as $todolist)
        {
        	$terms = explode('-', $todolist['alias']);
        	foreach($terms as $index=>$value)
        		if(strlen($value) < 3)
        			unset($terms[$index]);
        		
        	$todos = KService::get('com://site/todos.domain.entity.todo')
        			->getRepository()
        			->getQuery()
        			->disableChain()
        			->where('parent_id = '.$todolist['id'])
        			->fetchSet();
        				
        	foreach($todos as $todo)
        	{
        		foreach($terms as $term)
        			if(strlen($term) > 3)
        				if($todo->set('parent_id', $todolist['parent_id'])->set('description', $todo->description.' #'.trim($term))->addHashtag($term)->save())
        					dboutput($term.', ');
        	}		
        }
        
        
        dbexec('DELETE FROM #__anahita_nodes WHERE `type` LIKE \'%com:todos.domain.entity.todolist\' ');
        dbexec('DROP TABLE #__todos_todolists');
        
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