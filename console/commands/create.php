<?php 

namespace Console\Command;


use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    protected function configure()
    {
        $this->setName('init')
        ->setDescription('Initializes an anahita installation. By default the config.yaml file is read')
        ->setDefinition(array(
                new InputOption('config-env',null, InputOption::VALUE_OPTIONAL,'The config enviornment to use.','development'),
                new InputOption('no-config',null, InputOption::VALUE_NONE,'If set then it won\'t try to load the config file'),
                new InputOption('only-symlink',null, InputOption::VALUE_NONE,'Only performs a symlink'),
                new InputOption('db-name',null, InputOption::VALUE_REQUIRED,'Database name'),
                new InputOption('db-user',null, InputOption::VALUE_REQUIRED,'Database username'),
                new InputOption('db-password',null, InputOption::VALUE_REQUIRED,'Database password'),
                new InputOption('db-host',null, InputOption::VALUE_OPTIONAL,'Database host','127.0.0.1'),
                new InputOption('db-port',null, InputOption::VALUE_OPTIONAL,'Database port','3306'),
                new InputOption('db-prefix',null, InputOption::VALUE_OPTIONAL,'Database prefix','jos'),
                new InputOption('drop-db',null, InputOption::VALUE_NONE,'Drop existing database'),
                new InputOption('admin-name',null, InputOption::VALUE_OPTIONAL,'The admin name. This is only done for the first time installation', 'admin'),
                new InputOption('admin-password',null, InputOption::VALUE_OPTIONAL,'The admin password. This is only done for the first time installation','123123'),
                new InputOption('admin-email',null, InputOption::VALUE_OPTIONAL,'The admin email. This is only done for the first time installation','admin@example.com')
        ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {        
        $only_symlink = $input->getOption('only-symlink');

        install($this->getApplication()->getSrcPath(),
        $this->getApplication()->getSitePath(), !$only_symlink);
        $this->getApplication()->setEnv($input->getOption('config-env'));
        if ( $only_symlink )
            return;

        if ( !$input->getOption('no-config') )
        {        
            $config    = $this->getApplication()->getConfig();
            $db        = $config->database;            
        }

        if ( empty($db) || 
                !$db instanceof \KConfig ) {
            $db = new \KConfig();
        }
        
        $_GET = array(
                'db_name' => pick($db->name, $input->getOption('db-name')),
                'db_user' => pick($db->user, $input->getOption('db-user')),
                'db_password' => pick($db->password, $input->getOption('db-password')),
                'db_host' => pick($db->host, $input->getOption('db-host')),
                'db_port' => pick($db->port, $input->getOption('db-port')),
                'db_prefix' => str_replace('_','', pick($db->prefix, $input->getOption('db-prefix'))),
                'drop_db'   => $input->getOption('drop-db') ? 1 : 0,
                'admin_password' => $input->getOption('admin-password'),
                'admin_email'    => $input->getOption('admin-email'),
                'admin_name'     => $input->getOption('admin-name')
        );
            
        $config = config($this->getApplication()->getSitePath());
        $config['database']['prefix'] = str_replace('_', '', $config['database']['prefix']);
        $this->getApplication()->addConfig($config);        
    }
}

$console->addCommands(array(new \Console\Command\Create()));

?>