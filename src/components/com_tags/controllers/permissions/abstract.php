<?php

/**
 * Abstract Tag Permission.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class ComTagsControllerPermissionAbstract extends LibBaseControllerPermissionDefault
{

    /*
    *   Viewer object
    */
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
    *  Only admin can edit a tag
    *
    *  @return boolean
    */
    public function canEdit()
    {
        return $this->getItem()->authorize('edit');
    }

    /**
    *   Tags cannot be created via this controller
    *
    *   @return boolean ALWAYS FALSE
    */
    public function canAdd()
    {
        return false;
    }

    /**
    *  tags cannot be deleted via the controller
    *
    *  @return boolean ALWAYS FALSE
    */
    public function canDelete()
    {
        return false;
    }
}
