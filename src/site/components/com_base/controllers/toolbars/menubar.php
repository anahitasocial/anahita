<?php

/**
 * Menubar Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseControllerToolbarMenubar extends ComBaseControllerToolbarAbstract
{
    /**
     * Before Controller _actionRead is executed.
     *
     * @param KEvent $event Dispatcher event
     */
    public function onBeforeControllerGet(KEvent $event)
    {
        $this->getController()->menubar = $this;

        $title[] = strtoupper('COM-'.$this->getController()
                                          ->getIdentifier()
                                          ->package.'-'.$this
                                          ->getController()
                                          ->getIdentifier()
                                          ->name.'-PAGE-HEADER');
        $title[] = strtoupper('COM-'.$this->getController()
                                          ->getIdentifier()
                                          ->package.'-HEADER');

        $this->setTitle(translate($title));
    }

    /**
     * Adds a command to the menubar. A menubar command must always be used as means to navigation
     * through different views.
     *
     * @param string       $name   Command name
     * @param string       $label  Navigation label
     * @param string|array $url    Navigation URL
     * @param bool         $active Boolean to set if a navigation command is active
     *
     * @return ComBaseControllerToolbarMenubar
     */
    public function addNavigation($name, $label, $url, $active = false)
    {
        if (is_array($url)) {
            $url = http_build_query($url);
        }

        $command = $this->addCommand($name, array(
            'label' => $label,
        ))->getCommand($name)
          ->href($url);

        if ($active) {
            $command->class('active', ' ');
        }

        return $command;
    }
}
