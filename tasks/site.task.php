<?php

namespace Console;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

class Symlink extends Command
{
    protected function configure()
    {
        $this->setName('site:symlink')->setDescription('Symlinks the site');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeLn("<info>Linking files...</info>");
        $this->symlink();

        //removes legacy administrator directory
        $adminDirectory = WWW_ROOT.'/administrator';

        if (is_dir($adminDirectory)) {
            exec("rm -rf {$adminDirectory}");
        }
    }

    public function symlink()
    {
        $target = WWW_ROOT;
        $mapper = new \Installer\Mapper(ANAHITA_ROOT, $target);

        $patterns = array(
          '#^(components|libraries|media)/([^/]+)/.+#' => '\1/\2',
          '#^(cli)/.+#' => 'cli',
          '#^plugins/([^/]+)/([^/]+)/.+#' => 'plugins/\1/\2',
          '#^includes/.+#' => '\1/includes',
          '#^(vendors|migration)/.+#' => '',
          '#^configuration\.php-dist#' => '',
          '#^htaccess.txt#' => '',
        );

        $patterns['#^installation/.+#'] = '';
        $mapper->addCrawlMap('vendor/anahita-platform', $patterns);
        $mapper->addCrawlMap('src', $patterns);
        $mapper->symlink();
        $mapper->getMap('vendor/anahita-platform/index.php', 'index.php')->copy();
        $mapper->getMap('vendor/anahita-platform/htaccess.txt', '.htaccess')->copy();

        @mkdir($target.'/tmp', 0755);
        @mkdir($target.'/cache', 0755);
        @mkdir($target.'/log', 0755);

        $vendorLink = new \Installer\Map(COMPOSER_VENDOR_DIR, WWW_ROOT.'/vendor');
        $vendorLink->symlink();
    }
}

$config = new Config(WWW_ROOT);

if ($config->isConfigured()) {
	 $console->addCommands(array(new Symlink()));
}

class Create extends Command
{
    protected $_input;
    protected $_output;

    protected function configure()
    {
        $this->setName('site:init')
        ->setDescription('Initializes the site by linking necessary files and setting up the database')
        ->setDefinition(array(
            new InputOption('database-dump',null, InputOption::VALUE_OPTIONAL,'Use a database dump to initilaize the data'),
            new InputOption('database-name',null, InputOption::VALUE_REQUIRED,'Database name'),
            new InputOption('database-user',null, InputOption::VALUE_REQUIRED,'Database username'),
            new InputOption('database-password',null, InputOption::VALUE_REQUIRED,'Database password'),
            new InputOption('database-host',null, InputOption::VALUE_OPTIONAL,'Database host'),
            new InputOption('database-port',null, InputOption::VALUE_OPTIONAL,'Database port'),
            new InputOption('database-prefix',null, InputOption::VALUE_OPTIONAL,'Database prefix'),
            new InputOption('drop-database',null, InputOption::VALUE_NONE,'Drop existing database. <error>Use this command with care, as it will wipe off all exsiting data</error>'),
        ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_input = $input; 
        $this->_output = $output;
        
        $this->_symlink();
        $this->_configure();
    }

    protected function _symlink()
    {
        $this->_output->writeLn("<info>Linking files...</info>");
        $symlink = new Symlink();
        $symlink->symlink();
    }

    protected function _configure()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $output = $this->_output;
        $input  = $this->_input;

        $prompt = function($key, $text, $default = null, $error = null) use ($dialog, $output, $input)
        {
            $result = $input->getOption($key);

            if (empty($result) && !$input->getOption('no-interaction')) {

                if (!empty($default)) {
                    $text .= '(default: '.$default.') ';
                }

                while(strlen($result = $dialog->ask($output,'<info>'.$text.'</info>', $default)) == 0);

            } elseif (empty($result)) {

                $result = $default;

                if (empty($result)) {
                    $output->writeLn('<error>'.$error.'</error>');
                    exit(1);
                }
            }

            return $result;
        };

        $config = new Config(WWW_ROOT);

        $info = $config->getDatabaseInfo();

        if (! (isset($info['name'])
            && isset($info['user'])
            && isset($info['password'])
            && isset($info['host'])
            && isset($info['port'])
        )) {

            $config->setDatabaseInfo(array(
                'name'     => $prompt('database-name', 'Enter the name of the database? ', @$info['name'],'Please enter the database name'),
                'user'     => $prompt('database-user', 'Enter the database user? ', @$info['user'], 'Please enter the database user'),
                'password' => $prompt('database-password', 'Enter the database password? ', @$info['password'], 'Please enter the database password'),
                'host'     => $prompt('database-host', 'Enter the database host address? ', 'localhost', @$info['host']),
                'port'     => $prompt('database-port', 'Enter the database port? ', '3306', @$info['port']),
                'prefix'   => $prompt('database-prefix', 'Enter a prefix for the tables in the database? ', 'an_', @$info['prefix'])
            ));

            $output->writeLn('<info>connecting to database...</info>');

            $database = $config->getDatabaseInfo();

            $db = new \mysqli(
                $database['host'],
                $database['user'],
                $database['password'],
                '', //don't pass database name yet
                $database['port']
            );

            //check connection
            if ($db->connect_errno) {
                $errorMsg = sprintf("Connect failed: %s\n", mysqli_connect_error());
                $output->writeLn('<error>'.$errorMsg.'</error>');
                exit;
            }

            //check to see if database exists
            $db_exists = true;
            if (!$db->select_db($database['name'])) {
                $db_exists = false;
            }

            $dump_file = null;
            if ( $input->getOption('database-dump') ) {
                $dump_file = realpath($input->getOption('database-dump'));
            }

            if ($db_exists && $input->getOption('drop-database')) {
                $output->writeLn('<info>Dropping existing database...</info>');
                $db->query(sprintf("DROP DATABASE `%s`", $database['name']));
                $db_exists = false;
            }

            if (!$db_exists) {
                $output->writeLn('<info>Creating new database...</info>');

                if ($db->query(sprintf("CREATE DATABASE `%s` CHARACTER SET `utf8`", $database['name']))) {
                    $msg = sprintf("Database %s created successfully", $database['name']);
                    $output->writeLn('<info>'.$msg.'</info>');
                    $db->select_db($database['name']);
                    $db_exists = true;
                } else {
                    $errorMsg = sprintf("Coudln't create the database `%s`. Check your permissions and try again.", $database['name']);
                    $output->writeLn('<error>'.$errorMsg.'</error>');
                    exit;
                }
            }

            $sql_files = $dump_file ? array($dump_file) : array_map(function($file){
                                $file = ANAHITA_ROOT.'/vendor/anahita-platform/installation/sql/'.$file;
                                return $file;
                            }, array('schema.sql', 'data.sql'));

            $output->writeLn('<info>Populating database...</info>');

            $queries = '';
            foreach($sql_files as $file) {
                $content = file_get_contents($file);
                $queries .= str_replace('#__', $database['prefix'], $content);
            }

            if (!$db->multi_query($queries)) {
                $errorMsg = sprintf("Couldn't process file %s", $file);
                $output->writeLn('<error>'.$errorMsg.'</error>');
                exit;
            }

            $output->writeLn("<info>Anahita database populated successfully!</info>");
            $db->close();
        }

        $config->secret = bin2hex(openssl_random_pseudo_bytes(32));
        $config->save();
        
        $output->writeLn("<comment>Use site:signup to create the first user account</comment>"); 
    }
}

class Signup extends Command
{
    protected $_input;
    protected $_output;
    
    protected function configure()
    {
        $this->setName('site:signup')
        ->setDescription('Create the 1st person account as a Super Admin')
        ->setDefinition(array(
            new InputOption('admin-email',null, InputOption::VALUE_REQUIRED,'The first Super Admin email.'),
            new InputOption('admin-username',null, InputOption::VALUE_REQUIRED, 'The first Super Admin username'),
            new InputOption('admin-password',null, InputOption::VALUE_OPTIONAL, 'The first Super Admin password'),
        ));
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $this->_input = $input; 
        $this->_output = $output;
        
        $prompt = function($key, $text, $default = null, $error = null) use ($dialog, $output, $input)
        {
            $result = $input->getOption($key);

            if (empty($result) && !$input->getOption('no-interaction')) {

                if (!empty($default)) {
                    $text .= '(default: '.$default.') ';
                }

                while(strlen($result = $dialog->ask($output,'<info>'.$text.'</info>', $default)) == 0);

            } elseif (empty($result)) {

                $result = $default;

                if (empty($result)) {
                    $output->writeLn('<error>'.$error.'</error>');
                    exit(1);
                }
            }

            return $result;
        };
        
        $this->getApplication()->loadFramework();
        
        $isFirstUser = !(bool) \AnService::get('repos:people.person')->getQuery(true)->fetchValue('id');
                                    
        if (! $isFirstUser) {
            $msg = 'There are already people accounts in the system. You can only create the first person account using this command!';
            $output->writeLn('<error>' . $msg . '</error>');
            exit(1);
        }
        
        $password = random_password(16);
        $data = array(
            'usertype' => \ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR,
            'givenName' => 'Super',
            'familyName' => 'Admin',
            'email' => $prompt('admin-email', 'Enter admin email: ', ''),
            'username' => $prompt('admin-username', 'Enter admin username: ', 'superadmin'),
            'password' => $prompt('admin-password', 'Enter admin password: ', $password),
        );
            
        $person = \AnService::get('repos:people.person')->getEntity()->setData($data);

        if (! $person->validate()) {
            $errors = $person->getErrors();
            foreach ($errors as $error) {
                $output->writeLn('<error>'.$error->getMessage().'</error>');
            }
            exit(2);
        }
        
        $person->enable();
        
        if ($person->save()) {
            $output->writeLn('<info>Signed up the first person as Super Admin:</info>');
            $output->writeLn('<info>USERNAME: ' . $data['username'] .  '</info>');
            $output->writeLn('<info>PASSWORD: ' . $data['password'] .  '</info>');
            $output->writeLn("<comment>Point your browser to your Anahita installation and login.</comment>");
        } else {
            $output->writeLn("Something went wrong and cound't signup the first person!");
            exit(1);
        }        
        
    }
}

$config = new Config(WWW_ROOT);

if ($config->isConfigured()) {
    $console->addCommands(array(new Signup()));
}

$console->addCommands(array(new Create()));

class Signup extends Command
{
    protected $_input;
    protected $_output;
    
    protected function configure()
    {
        $this->setName('site:signup')
        ->setDescription('Signup the first person as super admin')
        ->setDefinition(array(
            new InputOption('admin-email',null, InputOption::VALUE_REQUIRED,'The first Super Admin email.'),
            new InputOption('admin-username',null, InputOption::VALUE_REQUIRED, 'The first Super Admin username'),
            new InputOption('admin-password',null, InputOption::VALUE_OPTIONAL, 'The first Super Admin password'),
        ));
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $this->_input = $input; 
        $this->_output = $output;
        
        $prompt = function($key, $text, $default = null, $error = null) use ($dialog, $output, $input)
        {
            $result = $input->getOption($key);

            if (empty($result) && !$input->getOption('no-interaction')) {

                if (!empty($default)) {
                    $text .= '(default: '.$default.') ';
                }

                while(strlen($result = $dialog->ask($output,'<info>'.$text.'</info>', $default)) == 0);

            } elseif (empty($result)) {

                $result = $default;

                if (empty($result)) {
                    $output->writeLn('<error>'.$error.'</error>');
                    exit(1);
                }
            }

            return $result;
        };
        
        $this->getApplication()->loadFramework();
        
        $isFirstUser = !(bool) \AnService::get('repos:people.person')->getQuery(true)->fetchValue('id');
                                    
        if (! $isFirstUser) {
            $msg = 'There are already people accounts in the system. You can only create the first person account using this command!';
            $output->writeLn('<error>' . $msg . '</error>');
            exit(1);
        }
        
        $password = random_password(16);
        $data = array(
            'usertype' => \ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR,
            'givenName' => 'Super',
            'familyName' => 'Admin',
            'email' => $prompt('admin-email', 'Enter admin email: ', ''),
            'username' => $prompt('admin-username', 'Enter admin username: ', 'superadmin'),
            'password' => $prompt('admin-password', 'Enter admin password: ', $password),
        );
            
        $person = \AnService::get('repos:people.person')->getEntity()->setData($data);

        if (! $person->validate()) {
            $errors = $person->getErrors();
            foreach ($errors as $error) {
                $output->writeLn('<error>'.$error->getMessage().'</error>');
            }
            exit(2);
        }
        
        $person->enable();
        
        if ($person->save()) {
            $output->writeLn('<info>Signed up the first person as Super Admin:</info>');
            $output->writeLn('<info>USERNAME: ' . $data['username'] .  '</info>');
            $output->writeLn('<info>PASSWORD: ' . $data['password'] .  '</info>');
        } else {
            $output->writeLn("Something went wrong and cound't signup the first person!");
            exit(1);
        }        
        
    }
}

$config = new Config(WWW_ROOT);

if ($config->isConfigured()) {
    $console->addCommands(array(new Signup()));
}

if (!$console->isInitialized()) {
    return;
}

$console
->register('site:configuration')
->setDescription('Provides the ability to set some of the site configuration through command line')
->setDefinition(array(
    new InputOption('enable-debug', '', InputOption::VALUE_NONE, 'Turn on the debug'),
    new InputOption('disable-debug', '', InputOption::VALUE_NONE, 'Turn off the debug'),
    new InputOption('new-secret', '', InputOption::VALUE_NONE, 'Generates a new secret'),
    new InputOption('url-rewrite', '', InputOption::VALUE_REQUIRED, 'Enable or disable url rewrite'),
    new InputOption('set-value', 's', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Setting key value pair',array()),
))
->setCode(function (InputInterface $input, OutputInterface $output) use ($console) {

    $config = new Config(WWW_ROOT);

    if (!$config->isConfigured()) {
        $output->writeLn("<error>You need to initialize the site first by typing php anahita.php site:init</error>");
        exit(1);
    }

    $set = function($name) use ($console, $config, $input)
    {
        $args = func_get_args();
        foreach($args as $arg) {
           $value = $input->getOption($arg);
           if ( strlen($value) > 0 ) {
               $arg   = str_replace('-','_',$arg);
               $config->$arg = $value;
           }
        }
    };

    $set('url-rewrite');

    if ($input->getOption('enable-debug')) {
        $config->enableDebug();
    } elseif ( $input->getOption('disable-debug') ) {
        $config->disableDebug();
    }

    if ($input->getOption('new-secret')) {
        $console->loadFramework();
        $config->set('secret', bin2hex(openssl_random_pseudo_bytes(32)));
    }

    if ($input->getOption('set-value')) {
        $values = $input->getOption('set-value');
        foreach ($values as $value) {
            $parts = explode('=',$value);
            $parts = array_map('trim', $parts);
            if ( count($parts) == 2 ) {
                $config->set($parts[0], $parts[1]);
            }
        }
    }

    $config->save();
});
