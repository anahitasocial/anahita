<?php

/**
 * Invite Default Contorller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComInvitesControllerDefault extends ComBaseControllerResource
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'toolbars' => array($this->getIdentifier()->name, 'menubar', 'actorbar'),
        ));

        parent::_initialize($config);
    }

    /**
     * If the user is not logged in then don't allow
     * read.
     *
     * @return bool
     */
    public function canGet()
    {
        return !$this->getService('com:people.viewer')->guest();
    }
}
