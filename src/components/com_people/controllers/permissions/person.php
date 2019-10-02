<?php

/**
 * People Permissions.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleControllerPermissionPerson extends ComActorsControllerPermissionDefault
{

    /**
     * Viewer object.
     *
     * @var ComPeopleDomainEntityPerson
     */
    protected $_viewer;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_mixer->permission = $this;
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
        $this->_viewer = get_viewer();

        parent::_initialize($config);
    }

    /**
     * return true if viewer is an admin or a guest.
     *
     * @return bool
     */
    public function canAdd()
    {
        if ($this->_viewer->admin()) {
            return true;
        }

        return false;
    }

    /**
     * return true if viewer has administration rights to the profile.
     *
     * @return bool
     */
    public function canEdit()
    {
        return $this->getItem()->authorize('administration');
    }

    /**
     * Return true if viewer is a guest.
     *
     * @return bool
     */
    public function canResetPassword()
    {
        return $this->_viewer->guest();
    }
}
