<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id: view.php 11985 2012-01-12 10:53:20Z asanieyan $
 *
 * @link       http://www.Anahita.io
 */

/**
 * Migration.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComMigratorMigrationVersion extends AnObject implements ArrayAccess
{
    /**
     * Database Adapter.
     * 
     * @var AnDatabaseAdapter
     */
    protected $_db;

    /**
     * The migration version.
     * 
     * @var int
     */
    protected $_version;

    /** 
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_db = $config->db;
        $this->_version = $config->version;
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'version' => $this->getIdentifier()->name,
            'db' => $this->getService('anahita:database'),
        ));

        parent::_initialize($config);
    }

    /**
     * Return the migration version.
     * 
     * @return int
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Called when we need to migrate up. By default it tries to run version.sql file.
     */
    public function up()
    {
        $file = dirname($this->getIdentifier()->filepath).'/'.$this->getVersion().'.sql';
        if (file_exists($file)) {
            dbexecfile($file);
        }
    }

    /**
     * Called when we need to migrate down. By default it tries to run the version.down.sql.
     */
    public function down()
    {
        $file = dirname($this->getIdentifier()->filepath).'/'.$this->getVersion().'.down.sql';
        if (file_exists($file)) {
            dbexecfile($file);
        }
    }

    /**
     * @param int $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return true;
    }

    /**
     * @param int $offset
     *
     * @return mixed The item from the array
     */
    public function offsetGet($offset)
    {
        return;
    }

    /**
     * Executes a query.
     *
     * @param int   $offset
     * @param mixed $value
     *
     * @return mixed
     */
    public function offsetSet($offset, $value)
    {
        dbexec($value);

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return mixed
     */
    public function offsetUnset($offset)
    {
        return $this;
    }
}
