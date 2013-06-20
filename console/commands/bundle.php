<?php 

namespace Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

class Bundle extends Command
{
    protected function configure()
    {
        $this->addArgument('bundles', InputArgument::IS_ARRAY, 'Name of the bundle');
        $this->addOption('schema', null, InputOption::VALUE_NONE, 'If set then it tries to run the database schema if found');
        $this->setName('bundle:install')
            ->setDescription('Install a bundle into the site');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bundles        = $input->getArgument('bundles');
        $search_paths[] = $this->getApplication()->getSrcPath().'/src/bundles';
        
        $directories = new \Console\Command\DirectoryIterator($bundles, $search_paths);
        
        if ( !count($directories) ) {
            throw new \RuntimeException('No valid bundle is specified');
        }
        $this->getApplication()->load();
        \KService::get('koowa:loader')
            ->loadIdentifier('com://admin/migrator.helper');
        foreach($directories as $dir)
        {                            
            $name   = ucfirst(basename($dir));
            $mapper = new \Installer\Mapper($dir, $this->getApplication()->getSitePath());
            $mapper->addCrawlMap('',  array(
                    '#^(site|administrator)/(components|modules|templates|media)/([^/]+)/.+#' => '\1/\2/\3',
                    '#CHANGELOG.php#'  => '',
                    '#^migration.*#'     => '',
                    '#manifest.xml#'   => ''
            ));
            $output->writeLn("<info>Linking $name Bundle</info>");
            $mapper->symlink();
            $this->_installExtensions($dir, $output, $input->getOption('schema'));
        }
    }

    protected function _installExtensions($dir, $output, $schema = false)
    {
        $files     = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        $manifests = array();
        foreach($files as $file) 
        {
           if ( $file->isFile() && 
                   $file->getExtension() == 'xml' )                     
           {             
               $xml     = new \SimpleXMLElement(file_get_contents($file));
               $install = array_pop($xml->xpath('/install'));               
               if ( $install && 
                       in_array($install['type'], array('component','plugin','module')) ) 
               {
                   $manifests[dirname($file)] = $install;
               }
           }
        }
        foreach($manifests as $dir => $manifest) 
        {
            $type     = $manifest['type'];
            $method   = '_install'.ucfirst($type);
            $name     = (string)$manifest->name[0].' '.$type;            
            $this->$method($manifest, $output, $dir, $schema);            
        }                        
    }
    
    protected function _installModule($manifest, $output)
    {
        $name     = strtolower((string)$manifest->name[0]);
        $output->writeLn("<info>...installing module $name</info>");
    }
    
    protected function _installPlugin($manifest, $output)
    {
        $plugins = \KService::get('repos:cli.plugin', 
                    array('resources'=>'plugins'));
        
        $group   = (string)$manifest->attributes()->group;        
        
        foreach($manifest->files->children() as $file)
        {
            if ( $name = (string)$file->attributes()->plugin )
            {
                $plugin = $plugins->findOrAddNew(array(
                     'element' => $name,
                     'folder'  => $group 
                ), array('data'=>array('params'=>'','published'=>true,'name'=>(string)$manifest->name)));                
                $plugin->saveEntity();                
                $output->writeLn("<info>...installing $group plugin $name</info>");
                return;
            }
        }        
    }
    
    protected function _installComponent($manifest, $output, $path, $schema)
    {        
        $name       = \KService::get('koowa:filter.cmd')->sanitize($manifest->name[0]);
        $name       = 'com_'.strtolower($name);
        
        
        $components = \KService::get('repos:cli.component', 
                    array('resources'=>'components'));
        
        //find or create a component
        $component  = $components->findOrAddNew(array('option'=>$name,'parent'=>0), 
                array('data'=>array('params'=>'')));
        
        //remove any child component
        $components->getQuery()
            ->option($name)
            ->parent('0','>')->destroy();
    
        $admin_menu = $manifest->administration->menu;        
        $site_menu  = $manifest->menu;
        
        $component->setData(array(
                'name'      => (string)$manifest->name[0],
                'enabled'   => 1,
                'link'      => '',
                'adminMenuLink' => '',
                'adminMenuAlt'  => '',
                'adminMenuImg'  => ''
        ));
        
        if ( $site_menu )
        {
            $component->setData(array(
                'link'      => 'option='.$name,
                'adminMenuLink' => 'option='.$name
            ));
        }
        elseif ( $admin_menu )
        {
            $component->setData(array(
                    'link'      => 'option='.$name,
                    'adminMenuLink' => 'option='.$name,
                    'adminMenuAlt'  => (string)$admin_menu,
                    'adminMenuImg'  => 'js/ThemeOffice/component.png'
            ));
        }   
        //first time installing the component then
        //run the schema
        if ( $component->isNew() ) {
            $schema = true;
        }         
        $output->writeLn('<info>...installing '.str_replace('com_','',$name).' component</info>');        
        $component->saveEntity();
        if ( $schema &&
                file_exists($path.'/schemas/schema.sql') ) 
        {
             $output->writeLn('<info>...running schema for '.str_replace('com_','',$name).' component</info>');
             $queries = dbparse(file_get_contents($path.'/schemas/schema.sql'));
             foreach($queries as $query) {
                 \KService::get('koowa:database.adapter.mysqli')
                     ->execute($query);
             }
        }
            
    }    
}


$console->addCommands(array(new \Console\Command\Bundle()));
$console
    ->register('bundle:list')
    ->setDescription('List the bundles')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($console) {
        $dirs = new \DirectoryIterator($console->getSrcPath().'/src/bundles');
        foreach($dirs as $dir) 
        {
            if ( $dir->isDir() && !$dir->isDot() ) {
                $output->writeLn("<info>".ucfirst($dir)."</info>");
            }
        }
    });
;



