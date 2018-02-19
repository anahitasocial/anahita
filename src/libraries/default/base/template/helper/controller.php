<?php
/**
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseTemplateHelperController extends LibBaseTemplateHelperAbstract
{
    /**
     * Static Controller Cache.
     *
     * @var array
     */
    protected static $_controllers = array();

    /**
     * Returns a toolbar object of a view.
     *
     * @return AnControllerToolbarAbstract|null If no toolbar found then null is returned
     */
    public function getToolbar()
    {
        $view = $this->_template->getView();
        $toolbar = $view->getState()->toolbar;
        if (!$toolbar) {
            $controller = $this->getController($view->getName());
            $toolbar = $controller->getIdentifier()->name;
            if ($controller->hasToolbar($toolbar)) {
                $toolbar = $controller->getToolbar($toolbar);
            }
            $controller->toolbar = $toolbar;
        }

        return $view->getState()->toolbar;
    }

    /**
     * Returns a controller object.
     *
     * @param string $name Controller name
     *
     * @return AnControllerAbstract
     */
    public function getController($name)
    {
        $name = AnInflector::singularize($name);

        if (!isset(self::$_controllers['controller.'.$name])) {
            if (strpos($name, '.') == false) {
                $identifier = clone $this->getIdentifier();
                $identifier->name = $name;
                $identifier->path = array('controller');
            } else {
                $identifier = $this->getIdentifier($name);
            }

            $controller = $this->getService($identifier, array('request' => array()));
            self::$_controllers['controller.'.$name] = $controller;
        }

        return self::$_controllers['controller.'.$name];
    }

    /**
     * Returns a controller object.
     *
     * @param string $name Controller name
     *
     * @return LibBaseViewAbstract
     */
    public function getView($name)
    {
        if (!isset(self::$_controllers['view.'.$name])) {
            $view = $this->getController($name)->setView($name)->getView();
            self::$_controllers['view.'.$name] = $view;
        }

        return self::$_controllers['view.'.$name];
    }
}
