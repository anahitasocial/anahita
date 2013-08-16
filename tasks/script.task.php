<?php 

if ( !$console->isInitialized() ) {
    return;
}

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

$console
->register('script')
->setDescription('Runs a PHP script after loading the Anahita framework')
->setDefinition(array(
    new InputArgument('script', InputArgument::OPTIONAL, 'Name of the components'),
))
->setCode(function (InputInterface $input, OutputInterface $output) use ($console) {
    $console->loadFramework();
    $script   = $input->getArgument('script');
    $run_code = function($code) use($console, $input, $output) {
        $script = tempnam(sys_get_temp_dir(), uniqid());
        $code   = str_replace('__DIR__', "'".getcwd()."'", $code);        
        file_put_contents($script, $code);
        require_once($script);
        unlink($script);
    };
    if ( empty($script) ) 
    {
        $stream = fopen('php://stdin','r');
        $data   = stream_get_contents($stream);
        fclose($stream);
        $run_code($data);
    } else {
        $parts = parse_url($script);
        //remote url
        if ( isset($parts['host']) ) {
            $run_code(file_get_contents($script));
        }
        else 
            require_once $script;
    }
});
?>