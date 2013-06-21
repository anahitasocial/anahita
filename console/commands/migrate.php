<?php 

namespace Console\Command;


use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

require_once 'vendor/nooku/libraries/koowa/event/subscriber/interface.php';
require_once 'vendor/nooku/libraries/koowa/object/handlable.php';


class Migrators implements \IteratorAggregate,\KEventSubscriberInterface , \KObjectHandlable
{
    protected $_migrators = array();
    
    protected $_event_dispatcher;
    
    protected $_output;
    
    public function __construct($console, $components)
    {
        $console->loadFramework();
        
        $components = array_map(function($item){
            return 'com_'.str_replace('com_','',$item);
        }, $components);      

        $paths = new DirectoryIterator($components, 
                array($console->getSitePath().'/administrator/components'));
        
        $this->_event_dispatcher = \KService::get('koowa:event.dispatcher');
        
        foreach($paths as $path) 
        {
            $component  = str_replace('com_','', basename($path));
            $identifier = 'com://admin/'.$component.'.schema.migration';
            register_default(array('identifier'=>$identifier,'default'=>'ComMigratorMigrationDefault'));
            $migrator   = \KService::get($identifier, 
                        array('event_dispatcher'=>$this->_event_dispatcher));
            if ( $migrator->getMaxVersion() > 0 ) {
                $this->_migrators[] = $migrator;
            }
        }
        
        $this->_event_dispatcher
            ->addEventListener('onAfterSchemaMigration',    $this);
    }
    
    public function setOutput($output)
    {
        $this->_event_dispatcher
            ->addEventListener('onBeforeSchemaVersionUp',   $this)
            ->addEventListener('onBeforeSchemaVersionDown', $this)        
            ->addEventListener('onBeforeSchemaMigration',   $this)
        ;        
        $this->_output = $output;    
    }
    
    public function getEventDispatcher()
    {
         return $this->_event_dispatcher;   
    }
    
    public function getIterator()
    {
        return new \ArrayIterator($this->_migrators);
    }
    
    /**
     * (non-PHPdoc)
     * @see KObjectHandlable::getHandle()
     */
    public function getHandle() {return spl_object_hash($this); }
    
    /**
     * (non-PHPdoc)
     * @see KEventSubscriberInterface::getPriority()
     */
    public function getPriority() { return 0; }
    
    /**
     * (non-PHPdoc)
     * @see KEventSubscriberInterface::getSubscriptions()
     */
    public function getSubscriptions() { return array(); }    
    
    /**
     * 
     * @param \KEvent $event
     */
    public function onBeforeSchemaVersionUp(\KEvent $event)
    {
        $name = $event->caller->getComponent();
        $this->_output->writeLn('<info>Migrating up '.$name.' version '.$event->version.'</info>');
    }

    /**
     * 
     * @param \KEvent $event
     */
    public function onBeforeSchemaVersionDown(\KEvent $event)
    {
        $name = $event->caller->getComponent();
        $this->_output->writeLn('<info>Rolling back '.$name.' version '.$event->version.'</info>');
    }

    /**
     *
     * @param \KEvent $event
     */
    public function onBeforeSchemaMigration(\KEvent $event)
    {
        if ( !count($event->versions) ) {
            $name = $event->caller->getComponent();
            $this->_output->writeLn('<info>There are no migrations to run for '.$name.'</info>');
        }
    }

    /**
     * 
     * @param \KEvent $event
     */
    public function onAfterSchemaMigration(\KEvent $event)
    {
        $event->caller->createSchema();
        $event->caller->write();
    }   
}

$console
->register('db:migrate:up')
->setDescription('Run the database migration for a component')
->setDefinition(array(
        new InputArgument('component', InputArgument::IS_ARRAY, 'Name of the components'),
))
->setCode(function (InputInterface $input, OutputInterface $output) use ($console) {
    $migrators  = new Migrators($console,
            $input->getArgument('component'));

    $migrators->setOutput($output);
    foreach($migrators as $migrator) {
        $migrator->up();
    }
});
;

$console
->register('db:migrate:rollback')
->setDescription('Rollback the database migration for a component')
->setDefinition(array(
        new InputArgument('component', InputArgument::IS_ARRAY, 'Name of the components'),
))
->setCode(function (InputInterface $input, OutputInterface $output) use ($console) {
    $migrators  = new Migrators($console,
            $input->getArgument('component'));

    $migrators->setOutput($output);
    foreach($migrators as $migrator) {
        $migrator->down();
    }
});

$console
    ->register('db:migrate:list')
    ->setDescription('list available migrations')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($console) {
        $dirs       = new \DirectoryIterator($console->getSitePath().'/administrator/components');
        $components = array();
        foreach($dirs as $dir) {
            if ( $dir->isDir() && !$dir->isDot() )
                $components[] = basename($dir);
        }
        $migrators  = new Migrators($console, $components);
            
        foreach($migrators as $migrator)
        {
            $component = $migrator->getComponent();
            $text = 'version '.$migrator->getCurrentVersion();
            if ( $behind = $migrator->getVersionsBehind() > 0 ) {
                $text .= ' behind '.$behind;
            }
            $output->writeLn('<info>'.$component.'</info> '.$text);            
        }
    });

$console
    ->register('db:schema:dump')
    ->setDescription('Dumps the schema for a component into its schema.sql file')
    ->setDefinition(array(
            new InputArgument('component', InputArgument::IS_ARRAY, 'Name of the components'),
    ))
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($console) {
        $migrators  = new Migrators($console,
                $input->getArgument('component'));
    
        $migrators->setOutput($output);
        foreach($migrators as $migrator) 
        {
            $migrator->createSchema();
            $migrator->write();
            $output->writeLn('<info>Dump database schema for com_'.$migrator->getComponent().'</info>');
        }
    });

?>