<?php

/**
 * Default Location Controller Toolbar
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsControllerToolbarDefault extends ComTagsControllerToolbarDefault
{
    /*
    *  holds the locatable object if exists
    */
    protected $_locatable = null;

    /**
    * Initializes the default configuration for the object.
    *
    * Called from {@link __construct()} as a first step of object instantiation.
    *
    * @param AnConfig $config An optional AnConfig object with configuration options.
    */
    protected function _initialize(AnConfig $config)
    {
        //@todo not crazy about this approach, but there was no way
        //that I could obtain the locatable from the controller
        $locatable_id = AnRequest::get('get.locatable_id', 'int', 0);

        if(!$this->_locatable && $locatable_id)
        {
            $this->_locatable = AnService::get('repos:nodes.node')
            ->getQuery()
            ->id($locatable_id)
            ->fetch();
        }

        parent::_initialize($config);
    }

    /**
     * Before Controller _actionRead is executed.
     *
     * @param AnEvent $event
     */
    public function onBeforeControllerRead(AnEvent $event)
    {
        if ($this->getController()->getItem()) {
            $this->addToolbarCommands();
        }
    }

    /**
    * Called before list commands.
    */
    public function addListCommands()
    {
        $this->getLocationCommands();
    }

    /**
    * Set the toolbar commands.
    */
    public function addToolbarCommands()
    {
        $this->getLocationCommands();
    }

    public function getLocationCommands()
    {
        $location = $this->getController()->getItem();

        if(!empty($this->_locatable)){
            $this->addCommand('deleteLocation');
        } else {
          if ($location->authorize('edit')) {
              $this->addCommand('edit');
          }

          if ($location->authorize('delete')) {
              $this->addCommand('delete');
          }
        }
    }
}
