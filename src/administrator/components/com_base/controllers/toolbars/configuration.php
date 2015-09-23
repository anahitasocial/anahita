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
class ComBaseControllerToolbarConfiguration extends ComBaseControllerToolbarDefault
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
     * Configuration browse only shows a save button.
     *
     * (non-PHPdoc)
     *
     * @see ComDefaultControllerToolbarDefault::onAfterControllerBrowse()
     */
    public function onAfterControllerBrowse(KEvent $event)
    {
        $this->addCommand('save');
    }
}
