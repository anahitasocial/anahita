<?php 

namespace Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

require_once 'vendor/nooku/libraries/koowa/event/subscriber/interface.php';
require_once 'vendor/nooku/libraries/koowa/object/handlable.php';

abstract class ComponentsMigrate extends ComponentsAbstract implements 
            \KEventSubscriberInterface , \KObjectHandlable
{
    /**
     * 
     * @var array
     */
    protected $_migrators;

    /**
     * 
     * @var
     */
    protected $_output;

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
        $name = ucfirst($event->caller->getComponent());
        $this->_output->writeLn('<info>Migrating '.$name.' Up to Version '.$event->version.'</info>');
    }

    /**
     * 
     * @param \KEvent $event
     */
    public function onBeforeSchemaVersionDown(\KEvent $event)
    {
        $name = ucfirst($event->caller->getComponent());
        $this->_output->writeLn('<info>Migrating '.$name.' Down to Version '.$event->version.'</info>');
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_from_dir  = $this->getApplication()->getSitePath().'/administrator/components';
        $components = array_map(function($item){
            return 'com_'.str_replace('com_','',$item);
        }, $input->getArgument('components'));
        $input->setArgument('components', $components);
        parent::execute($input, $output);
        $this->_components = array_map(function($item){return str_replace('com_','',basename($item));},$this->_components);
        $target     = $this->getApplication()->getSitePath();
        define('JPATH_BASE', $target.'/administrator');
        require_once ( JPATH_BASE.'/includes/framework.php' );
        $_SERVER['HTTP_HOST'] = '';
        \KService::get('com://admin/application.dispatcher')->load();
        
        $event_dispatcher = \KService::get('koowa:event.dispatcher');
        $this->_output    = $output;
        $event_dispatcher
            ->addEventListener('onBeforeSchemaVersionUp',   $this)
            ->addEventListener('onBeforeSchemaVersionDown', $this)
            ->addEventListener('onAfterSchemaMigration',    $this)
            ;
        foreach($this->_components as $component) 
        {
            $identifier = 'com://admin/'.$component.'.schema.migration';
            register_default(array('identifier'=>$identifier,'default'=>'ComMigratorMigrationDefault'));
            $this->_migrators[] = \KService::get($identifier, array('event_dispatcher'=>$event_dispatcher));
        }
    }
}

/**
 *
 * @author asanieyan
 *
 */
class ComponentsMigrateVersion extends ComponentsMigrate
{
    /**
     *
     */
    protected function configure()
    {
        parent::configure();
    
        $this->setName('db:migrate:version')
            ->setDescription('Show migration versions');
    }
        
    /**
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
    
        foreach($this->_migrators as $migrator) {
            $output->writeLn('<info>'.ucfirst($migrator->getComponent()).' '.$migrator->getCurrentVersion().'</info>');
        }
    }    
}

/**
 * 
 * @author asanieyan
 *
 */
class ComponentsMigrateUp extends ComponentsMigrate
{
    /**
     * 
     */
    protected function configure()
    {
        parent::configure();
        
        $this->setName('db:migrate:up')
            ->setDescription('Migrate components');
    }
     
    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
              
        foreach($this->_migrators as $migrator) {
            $migrator->up();
        }
    }
}

/**
 * 
 * @author asanieyan
 *
 */
class ComponentsMigrateDown extends ComponentsMigrate
{
    /**
     * 
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('db:migrate:rollback')
            ->setDescription('Rollback a component migration');
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
  
       
        foreach($this->_migrators as $migrator) {
            $migrator->rollback();
        }
    }
}

?>