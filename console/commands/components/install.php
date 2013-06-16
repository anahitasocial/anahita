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
    
        $this->setName('components:install')
            ->setDescription('Install components');
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
            $this->_from_dir  = $this->getApplication()->getSrcPath().'/src/components';
        }
    
        parent::execute($input, $output);
    
        $target     = $this->getApplication()->getSitePath();
        
        foreach($this->_components as $component) {
            $output->writeLn('Installing '.basename($component));
            shell_exec("php $target/cli/install.php $component");
        }
    }    
}