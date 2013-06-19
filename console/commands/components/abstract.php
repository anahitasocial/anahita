<?php 

namespace Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

abstract class ComponentsAbstract extends Command
{
    /**
     * Array of components
     * 
     * @var array
     */
    protected $_components;
    
    /**
     * Components directory
     * 
     * @var components directory
     */
    protected $_from_dir;
    
    /**
     * 
     */
    protected function configure()
    {
        $this->addArgument('components', InputArgument::IS_ARRAY, 'Component names');
        //$this->addOption('all-except','x', InputOption::VALUE_OPTIONAL,'Components to skip');      
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \InvalidArgumentException
     * @return boolean
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $target     = $this->getApplication()->getSitePath();
        $path       = $this->_from_dir;        
        if ( !is_dir($path) ) {
            throw new \InvalidArgumentException('Components folder doest not exists');
        }
        $dirs       = new \DirectoryIterator($path.'/');
        $components = array();
        foreach($dirs as $dir)
        {
            if ( !$dir->isDot() && $dir->isDir() ){
                $components[] = $dir->getPath().'/'.$dir;
            }
        }
        $only   = $input->getArgument('components');
        
        if ( empty($only) ) {
            throw new \RuntimeException('No component is specified');
        }
        
        if ( !empty($only) )
        {
            $components = array_filter($components, function($component) use($only) {
                $name = basename($component);
                return in_array($name, $only);
            });
        }
        $this->_components = $components;
        //$skip = explode(',', $input->getOption('all-except'));
        
//         $this->_components = array_filter($components, function($component) use($skip) {
//             $name = basename($component);
//             return !in_array($name, $skip);
//         });
    }    
}
?>