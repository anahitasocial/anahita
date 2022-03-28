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
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * Storable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class LibBaseDomainBehaviorStorable extends AnDomainBehaviorAbstract
{
    /**
     * Storage Adapter.
     *
     * @var AnStorageAdapterAbstract
     */
    protected $_storage;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_storage = $config->storage;
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
             'storage' => $this->getService('plg:storage.default'),
        ));

        parent::_initialize($config);
    }

    /**
     * Return the storage path of an entity. If $path is passed, it will
     * append the $path to the base storage path.
     * 
     * @param string $path The path to append the storage path with
     * 
     * @return string
     */
    public function getStoragePath($path = '')
    {
        //prepend the path with a \/
        if (strlen($path)) {
            $path = '/'.$path;
        }

        $base = $this->_mixer->id;

        //for ownable entities, use the owner component to prefix
        //the path
        if ($this->_mixer->isOwnable()) {
            $path = '/'.$this->_mixer->component.$path;
            $base = $this->_mixer->owner->id;
        }

        return 'n'.$base.$path;
    }

    /**
     * Write data to entity storage.
     *
     * @param string $path   The relative path to store the data in
     * @param string $data   The data to store
     * @param bool   $public The storage mode. Can be public or private
     */
    public function writeData($path = '', $data, $public = true)
    {
        $path = $this->getStoragePath($path);

        return $this->_storage->write($path, $data, $public);
    }

    /**
     * Read data from entity storage.
     *
     * @param string $path   The relative path to read the data from
     * @param bool   $public The storage mode. Can be public or private
     *
     * @return string
     */
    public function readData($path = '', $public = true)
    {
        $path = $this->getStoragePath($path);

        return $this->_storage->read($path, $public);
    }

    /**
     * Delete an existing data with the path.
     *
     * @param string $path   The relative path to delete the data from
     * @param bool   $public The storage mode. Can be public or private
     */
    public function deletePath($path = '', $public = true)
    {
        $path = $this->getStoragePath($path);
        return $this->_storage->delete($path, $public);
    }

    /**
     * Checks the existance of path.
     *
     * @param string $path   The relative path to check
     * @param bool   $public The storage mode. Can be public or private
     *
     * @return bool
     */
    public function pathExists($path = '', $public = true)
    {
        $path = $this->getStoragePath($path);
        return $this->_storage->exists($path, $public);
    }

    /**
     * Gets the unique identifiable location (URL) for a given path.
     *
     * @param string $path   The relative path to check
     * @param bool   $public The storage mode. Can be public or private
     *
     * @return bool
     */
    public function getPathURL($path = '', $public = true)
    {
        $path = $this->getStoragePath($path);
        return $this->_storage->getUrl($path, $public);
    }

    /**
     * Completely remove the storage.
     */
    public function removeStorage()
    {
        $this->deletePath('', true);
        $this->deletePath('', false);
    }

    /**
     * Before delete command.
     *
     * When an entity is deleted, the call of this command removes the deleted entity storage
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _beforeEntityDelete(AnCommandContext $context)
    {
        $entity = $context->entity;
        $entity->removeStorage();
    }
}
