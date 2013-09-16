<?php 

namespace Console;

if ( !$console->isInitialized() ) {
    return;
}

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

class PackageCommand extends Command
{
    protected function configure()
    {
        $this->addArgument('package', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Name of the package');                
        $this->addOption('create-schema', null, InputOption::VALUE_NONE, 'If set then it tries to run the database schema if found');
        $this->setName('package:install')
            ->setDescription('Install a package into the site');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packages = $input->getArgument('package');

        $packages = $this->getApplication()
             ->getExtensionPackages()
             ->findPackages($packages)             
            
        ;
        
        if ( !count($packages) ) {
            throw new \RuntimeException('Invalid Packages');
        }
        
        $this->getApplication()->loadFramework();
        \KService::get('koowa:loader')
            ->loadIdentifier('com://admin/migrator.helper');
 
        /*        
        $helper = $this->getHelperSet();
        $io     = new \Composer\IO\ConsoleIO($input, $output, $helper);
        $get_composer = function($root) use($input, $output, $helper,$io) {
            global $composerLoader;
            $embeddedComposerBuilder = new \Dflydev\EmbeddedComposer\Core\EmbeddedComposerBuilder($composerLoader, $root);
            return $embeddedComposerBuilder->build();            
        };
        */
                
        foreach($packages as $package)
        {
            $mapper   = new \Installer\Mapper($package->getSourcePath(), WWW_ROOT);
            $mapper->addCrawlMap('',  array(
                    '#^(site|administrator)/(components|modules|templates|media)/([^/]+)/.+#' => '\1/\2/\3',
                    '#^(media)/([^/]+)/.+#' => '\1/\2',
                    '#CHANGELOG.php#'  => '',
                    '#^migration.*#'     => '',
                    '#manifest.xml#'   => ''
            ));
            $output->writeLn("<info>Linking {$package->getFullName()} Package</info>");
            $mapper->symlink();           
            
            
//             $root     = $get_composer(COMPOSER_ROOT)->createComposer($io);
//             $app      = $get_composer($package->getRoot())->createComposer($io);
                        
//             $requires = $root->getPackage()->getRequires();
            
//             foreach($app->getPackage()->getRequires() as $require) 
//             {
//                 $output->writeLn((string)$require);
//                 //$require    = new \Composer\Package\Link('__root__',$require->getTarget(), $require->getConstraint(), null, $require->getPrettyConstraint());
//                 $requires[] = $require;
//             }

//             $root->getPackage()->setRequires($requires);
//             $installer = \Composer\Installer::create($io, $root);            
            
//             $installer->setDryRun(true);
//             $installer->setUpdate(true);
//             $installer->run();
            
            $this->_installExtensions($package->getSourcePath(), $output, $input->getOption('create-schema'));
        }        
    }

    protected function _installExtensions($dir, $output, $schema = false)
    {
        $files     = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        $manifests = array();
        foreach($files as $file) 
        {
           if ( $file->isFile() && 
                   pathinfo($file->getFilename(), PATHINFO_EXTENSION) == 'xml' )                     
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
                ), array('data'=>array('params'=>'','published'=>true)));
                $plugin->name = (string)$manifest->name;                
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
                'link'      => 'option='.$name
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


$console
->register('package:uninstall')
->setDescription('Uninstalls a package')
->setDefinition(array(
        new InputArgument('package', InputArgument::IS_ARRAY, 'Name of the package'),
))
->setCode(function (InputInterface $input, OutputInterface $output) use ($console) {

        $packages = $input->getArgument('package');
        
        $packages = $console->getExtensionPackages()
                ->findPackages($packages);
        
        if ( !count($packages) ) {
            throw new \RuntimeException('Specify a valid package');
        }
        foreach($packages as $package) 
        {
            $mapper = new \Installer\Mapper($package->getSourcePath(), WWW_ROOT);
            $mapper->addCrawlMap('',  array(
                    '#^(site|administrator)/(components|modules|templates|media)/([^/]+)/.+#' => '\1/\2/\3',
                    '#^(media)/([^/]+)/.+#' => '\1/\2',
                    '#CHANGELOG.php#'    => '',
                    '#^migration.*#'     => '',
                    '#manifest.xml#'     => ''
            ));            
            $output->writeLn("<info>Unlinking {$package->getFullName()} Package</info>");
            $mapper->unlink();           
        }
});
;

$console->addCommands(array(new PackageCommand()));
$console
    ->register('package:list')
    ->setDescription('List of packages')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($console) {
        
        $vendros = array_group_by($console->getExtensionPackages(), function($package) {
                return $package->getVendor();
        });
        
        foreach($vendros as $vendor => $packages)
        {
            $output->writeLn("<info>".$vendor."</info>");
            foreach($packages as $package) {
                $output->writeLn("<info> - ".$package->getName()."</info>");
            }
        }
    })
    ;
;



