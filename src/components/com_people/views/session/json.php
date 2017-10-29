<?php

/**
 * Search result in Html format.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleViewSessionJson extends ComBaseViewJson
{
    /**
     * (non-PHPdoc).
     *
     * @see LibBaseViewJson::display()
     */
    public function display()
    {
        if (!$this->_state->getItem()) {
            throw new LibBaseControllerExceptionUnauthorized('User is not logged in');
        } else {
            return parent::display();
        }
    }
}
