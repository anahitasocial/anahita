<?php

/**
 * Default Actor Permission.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsControllerPermissionDefault extends LibBaseControllerPermissionDefault
{
    protected $_viewer;

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        parent::_initialize($config);

        $this->_viewer = get_viewer();
    }

    /**
    *  if viewer has permission to add a location
    *
    *  @return boolean
    */
    public function canAdd()
    {
        return $this->_viewer->admin();
    }

    /**
    *  if viewer has permission to edit a location
    *
    *  @return boolean
    */
    public function canEdit()
    {
        return $this->_viewer->admin();
    }

    /**
    *  if viewer has permission to delete a location
    *
    *  @return boolean
    */
    public function canDelete()
    {
        return $this->_viewer->admin();
    }
}
