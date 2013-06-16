<?php 


        
class ComponentsMigrate extends CommandAbstract implements \KEventSubscriberInterface , \KObjectHandlable
{
    protected $_migrator;
    
    protected $_output;
    
    protected function configure()
    {
        parent::configure(); 
    }

    public function getHandle() {return spl_object_hash($this); }
    public function getPriority() { return 0; }
    public function getSubscriptions() { return array(); }
    
        
    public function onBeforeSchemaVersionUp(\KEvent $context)
    {        
        $name = ucfirst($context->caller->getComponent());
        $this->_output->writeLn('<info>Migrating '.$name.' Up : Version '.$context->version.'</info>');        
    }       
    
    public function onBeforeSchemaVersionDown(\KEvent $context)
    {
        $name = ucfirst($context->caller->getComponent());
        $this->_output->writeLn('<info>Migrating '.$name.' Down : Version '.$context->version.'</info>');
    }
    
    public function onAfterSchemaMigration(\KEvent $event)
    {
        $event->caller->createSchema();
        $event->caller->write();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->from_dir  = $this->getApplication()->getSitePath().'/administrator/components';
        $components = array_map(function($item){
            return 'com_'.str_replace('com_','',$item);
        }, $input->getArgument('components'));
        $input->setArgument('components', $components);
        parent::execute($input, $output);
        $this->components = array_map(function($item){return str_replace('com_','',basename($item));},$this->components);
        $target     = $this->getApplication()->getSitePath();
        define('JPATH_BASE', $target.'/administrator');
        require_once ( JPATH_BASE.'/includes/framework.php' );
        $_SERVER['HTTP_HOST'] = '';
        \KService::get('com://admin/application.dispatcher')->load();
        $this->_output   = $output;
        
        $this->_migrator = \KService::get('com://admin/migrator.controller.default'); 
        
        $this->_migrator->getEventDispatcher()
                    ->addEventListener('onBeforeSchemaVersionUp', $this)
                    ->addEventListener('onBeforeSchemaVersionDown', $this)
                    ->addEventListener('onAfterSchemaMigration', $this)
                ;        
    }    
}





?>