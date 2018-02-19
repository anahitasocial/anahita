<?php

/**
 * Toolbar Helper.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseTemplateHelperToolbar extends LibBaseTemplateHelperAbstract
{
    /**
     * Return a container of commands by calling add[Name]Commands on the toolbar
     * object. If the toolbar is not set then.
     *
     * @param string $name The command set name
     * @param array  $data Data pass to the controller toolbar
     *
     * @return LibBaseTemplateObjectContainer
     */
    public function commands($name, $data = array())
    {
        $toolbar = $this->_template->getHelper('controller')->getToolbar();

        if (isset($data['clone'])) {
            $toolbar = clone $toolbar;
        }

        if ($toolbar instanceof AnControllerToolbarAbstract) {
            //reset the toolbar
            $toolbar->reset();

            $method = 'add'.ucfirst($name).'Commands';

            if (method_exists($toolbar, $method)) {
                $toolbar->$method();
            }

            return $toolbar->getCommands();
        }
    }
}
