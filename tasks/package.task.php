<?php
namespace Console;

if (!$console->isInitialized()) {
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
        $this->setName('package:install')->setDescription('Install a package into the site');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packages = $input->getArgument('package');
        $packages = $this->getApplication()->getExtensionPackages()->findPackages($packages);

        if (!count($packages)) {
            throw new \RuntimeException('Invalid Packages');
        }

        $this->getApplication()->loadFramework();
        \KService::get('koowa:loader')->loadIdentifier('com://site/migrator.helper');

        foreach ($packages as $package) {

            $mapper = new \Installer\Mapper($package->getSourcePath(), WWW_ROOT);

            $mapper->addCrawlMap('', array(
              '#^(components|templates|media)/([^/]+)/.+#' => '\1/\2',
              '#^(media)/([^/]+)/.+#' => '\1/\2',
              '#CHANGELOG.php#' => '',
              '#^migration.*#' => '',
              '#^component.json#' => ''
            ));

            $output->writeLn("<info>Linking {$package->getFullName()} Package</info>");
            $mapper->symlink();

            $this->_installExtensions($package->getSourcePath(), $output, $input->getOption('create-schema'));
        }
    }

    protected function _installExtensions($dir, $output, $schema = false)
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        $manifests = array();

        foreach ($files as $file) {
          if ($file->isFile() && pathinfo($file->getFilename(), PATHINFO_EXTENSION) == 'json') {

               $json = json_decode(file_get_contents($file));
               if (isset($json->type) && in_array($json->type, array('component', 'plugin'))) {
                  $manifests[dirname($file)] = $json;
               }
           }
        }

        foreach ($manifests as $dir=>$manifest) {
            $type = $manifest->type;
            $method = '_install'.ucfirst($type);
            $name = (string) $manifest->name.' '.$type;
            $this->$method($manifest, $output, $dir, $schema);
        }
    }

    protected function _installPlugin($manifest, $output)
    {
        $plugins = \KService::get('repos:cli.plugin', array('resources' => 'plugins'));

        $group = (string) $manifest->group;

        foreach ($manifest->files as $file) {
            if ($element = (string) str_replace('.php', '', $file)) {

                $plugin = $plugins->findOrAddNew(array(
                  'element' => $element,
                  'folder'  => $group
                ), array('data' => array('meta'=>'', 'published' => true)));

                $plugin->name = (string) $manifest->name;
                $plugin->saveEntity();
                $output->writeLn("<info>...installing $group plugin $element </info>");

                return;
            }
        }
    }

    protected function _installComponent($manifest, $output, $path, $schema)
    {
        $name = \KService::get('koowa:filter.cmd')->sanitize($manifest->name);
        $name = 'com_'.strtolower($name);
        $components = \KService::get('repos:cli.component', array('resources'=>'components'));

        //find or create a component
        $component = $components->findOrAddNew(array(
                        'option' => $name,
                        'parent' => 0
                      ),
                      array('data' => array('meta'=>'')
                    ));

        //remove any child component
        $components->getQuery()->option($name)->parent('0', '>')->destroy();

        $component->setData(array(
          'name' => (string) $manifest->name,
          'enabled' => 1
        ));

        //first time installing the component then
        //run the schema
        if ($component->isNew()) {
            $schema = true;
        }

        $output->writeLn('<info>...installing '.str_replace('com_','',$name).' component</info>');
        $component->saveEntity();

        if ($schema && file_exists($path.'/schemas/schema.sql')) {

            $output->writeLn('<info>...running schema for '.str_replace('com_','',$name).' component</info>');
            $queries = dbparse(file_get_contents($path.'/schemas/schema.sql'));

            foreach ($queries as $query) {
                 \KService::get('anahita:database')->execute($query);
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
    $packages = $console->getExtensionPackages()->findPackages($packages);

      if (!count($packages)) {
          throw new \RuntimeException('Specify a valid package');
      }

      foreach ($packages as $package) {

          $mapper = new \Installer\Mapper($package->getSourcePath(), WWW_ROOT);
          $mapper->addCrawlMap('',  array(
            '#^(components|templates|media)/([^/]+)/.+#' => '\1/\2',
            '#^(media)/([^/]+)/.+#' => '\1/\2',
            '#CHANGELOG.php#' => '',
            '#^migration.*#' => '',
            '#^component.json#' => ''
          ));
          $output->writeLn("<info>Unlinking {$package->getFullName()} Package</info>");
          $mapper->unlink();
      }

      //Delete empty language directories
      $languagePath = WWW_ROOT.'/language/';
      foreach (scandir($languagePath) as $node) {
          if (is_dir($languagePath.$node) && strpos($node, '-') > -1 && $node != 'en-GB') {
              $empty = true;
              foreach(scandir($languagePath.$node) as $file) {
                  $fileParts = pathinfo($file);
                  if ($file_parts['extension'] == 'ini') {
                      $empty = false;
                  }
              }
              if ($empty) {
                  rmdir($languagePath.$node);
              }
          }
      }
});

$console->addCommands(array(new PackageCommand()));

$console
->register('package:list')
->setDescription('List of packages')
->setCode(function (InputInterface $input, OutputInterface $output) use ($console) {

    $vendros = array_group_by($console->getExtensionPackages(), function($package) {
        return $package->getVendor();
    });

    foreach ($vendros as $vendor => $packages) {
        $output->writeLn("<info>".$vendor."</info>");
        foreach ($packages as $package) {
            $output->writeLn("<info> - ".$package->getName()."</info>");
        }
    }
});
