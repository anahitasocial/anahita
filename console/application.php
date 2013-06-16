<?php 

namespace Console;

require_once 'console/commands/create.php';
require_once 'console/commands/components/abstract.php';
require_once 'console/commands/components/install.php';
require_once 'console/commands/components/migrate.php';

require_once 'console/config.php';


use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

class Application extends \Symfony\Component\Console\Application
{
    protected $site;
    protected $src;

    public function __construct($src, $site)
    {
        $this->src  = $src;
        $this->site = $site;

        parent::__construct($site);

        $this->addCommands(array(new \Console\Command\Create()));
        $this->addCommands(array(new \Console\Command\ComponentsInstall()));
        $this->addCommands(array(new \Console\Command\ComponentsMigrateUp()));
        $this->addCommands(array(new \Console\Command\ComponentsMigrateDown()));
        $this->addCommands(array(new \Console\Command\ComponentsMigrateVersion()));
    }

    public function getSrcPath()
    {
        return $this->src;
    }

    public function getSitePath()
    {
        return $this->site;
    }
}

?>