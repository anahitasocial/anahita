<?php 

require_once 'scripts/class.php';
require_once 'scripts/config.php';
require_once 'vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

$console = new Application();

$console
    ->register('setup')
    ->setDefinition(array(
    ))
    ->setDescription('setup an anahita installation')
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        install(__DIR__, __DIR__.'/site');
    });
    
$console
        ->register('install-anahita')
        ->setDefinition(array(
                new InputOption('db-name',null, InputOption::VALUE_REQUIRED,'Database name'),
                new InputOption('db-user',null, InputOption::VALUE_REQUIRED,'Database username'),
                new InputOption('db-password',null, InputOption::VALUE_REQUIRED,'Database password'),
                new InputOption('db-host',null, InputOption::VALUE_OPTIONAL,'Database host','127.0.0.1'),                                                
                new InputOption('db-port',null, InputOption::VALUE_OPTIONAL,'Database port','3306'),
                new InputOption('db-prefix',null, InputOption::VALUE_OPTIONAL,'Database prefix','jos'),
                new InputOption('drop-db',null, InputOption::VALUE_NONE,'Drop existing database'),
                new InputOption('admin-name',null, InputOption::VALUE_OPTIONAL,'The admin name. This is only done for the first time installation'),
                new InputOption('admin-password',null, InputOption::VALUE_OPTIONAL,'The admin password. This is only done for the first time installation'),                
                new InputOption('admin-email',null, InputOption::VALUE_OPTIONAL,'The admin email. This is only done for the first time installation')                                                                                                                                                                
        ))
        ->setDescription('setup and configure an anahita installation')
        ->setCode(function (InputInterface $input, OutputInterface $output) 
        {
            install(__DIR__, __DIR__.'/site', true);
            $_GET = array(
              'db_name' => $input->getOption('db-name'),
              'db_user' => $input->getOption('db-user'),
              'db_password' => $input->getOption('db-password'),
              'db_host' => $input->getOption('db-host'),
              'db_port' => $input->getOption('db-port'),
              'db_prefix' => $input->getOption('db-prefix'),    
              'drop_db'   => $input->getOption('drop-db') ? 1 : 0,     
              'admin_password' => $input->getOption('admin-password'),
              'admin_email'    => $input->getOption('admin-email'),
              'admin_name'     => $input->getOption('admin-name')                                                                                                        
            );
            
            config(realpath(__DIR__.'/site'));
        });    
        
$console
            ->register('install-components')
            ->setDefinition(array(       
                    new InputArgument('components', InputArgument::IS_ARRAY, 'Component names'),
                    new InputOption('skip',null, InputOption::VALUE_OPTIONAL,'Components to skip'),                    
                    new InputOption('components-directory',null, InputOption::VALUE_OPTIONAL,'Components Directory',__DIR__.'/src/components'),
            ))
            ->setDescription('setup an anahita installation')
            ->setCode(function (InputInterface $input, OutputInterface $output) {              
                $target     = __DIR__.'/site';
                $path       = $input->getOption('components-directory');
                if ( !is_dir($path) ) {
                    throw new InvalidArgumentException('Components folder doest not exists');
                }
                $dirs       = new DirectoryIterator($path.'/');
                $components = array();
                foreach($dirs as $dir)
                {
                    if ( !$dir->isDot() && $dir->isDir() ){
                        $components[] = $dir->getPath().'/'.$dir;
                    }
                }
                $only   = $input->getArgument('components');
                if ( !empty($only) ) 
                {
                    $components = array_filter($components, function($component) use($only) {
                        $name = basename($component);
                        return in_array($name, $only);
                    });
                }
                
                $skip = explode(',', $input->getOption('skip'));
                $components = array_filter($components, function($component) use($skip) {
                    $name = basename($component);
                    return !in_array($name, $skip);
                });  
                foreach($components as $component) 
                {
                    $output->writeLn('Installing '.basename($component));
                    shell_exec("php $target/cli/install.php $component");    
                }
            });        
                
$console->run();    