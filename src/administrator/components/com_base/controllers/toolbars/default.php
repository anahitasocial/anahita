<?php

/**
 * Resource Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseControllerToolbarDefault extends KControllerToolbarAbstract
{
    /**
     * Push the toolbar into the view
     * .
     *
     * @param	KEvent	A event object
     */
    public function onBeforeControllerGet(KEvent $event)
    {
        KService::set('com:controller.toolbar', $this);
        $event->getPublisher()->getView()->toolbar = $this;
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $package = KInflector::humanize($this->getIdentifier()->package);
        $name = KInflector::humanize(KInflector::pluralize($this->getName()));

        $config->append(array(
            'title' => $package.' - '.$name,
        ));

        parent::_initialize($config);
    }

    /**
     * Add default toolbar commands and set the toolbar title
     * .
     *
     * @param	KEvent	A event object
     */
    public function onAfterControllerRead(KEvent $event)
    {
        $name = ucfirst($this->getController()->getIdentifier()->name);
        $saveable = false;

        if ($this->getController()->getState()->isUnique()) {
            $saveable = $this->getController()->canEdit();
            $title = 'Edit '.$name;
        }

        if ($saveable) {
            $this->setTitle($title)->addCommand('save');
        }

        $this->addCommand('cancel',  array('attribs' => array('data-novalidate' => 'novalidate')));
    }

    /**
     * Add default toolbar commands
     * .
     *
     * @param	KEvent	A event object
     */
    public function onAfterControllerBrowse(KEvent $event)
    {
        return;
    }
}
