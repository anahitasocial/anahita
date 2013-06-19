<?php 

namespace Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

class ComponentsInstall extends ComponentsAbstract
{
    /**
     * 
     */
    protected function configure()
    {
        parent::configure();
    
        $this->addOption('from-directory',null, InputOption::VALUE_OPTIONAL,'Directory to install components from');
    
        $this->setName('install')
            ->setDescription('Install components from a list of bundles');
    }
    
    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_from_dir = $input->getOption('from-directory');
    
        if ( empty($this->_from_dir) ) {
            $this->_from_dir  = $this->getApplication()->getSrcPath().'/src/bundles';
        }
    
        parent::execute($input, $output);
    
        $target     = $this->getApplication()->getSitePath();
               
        foreach($this->_components as $component) 
        {
            $name   = ucfirst(basename($component));
            $mapper = new \Installer\Mapper($component, $target);
            $mapper->addCrawlMap('',  array(
                '#^(site|administrator)/(components|modules|templates|media)/([^/]+)/.+#' => '\1/\2/\3',                    
                '#CHANGELOG.php#'  => '',
                '#migration#'     => '',
                '#manifest.xml#'   => ''        
            ));
            $mapper->symlink();
        }
    }    
}