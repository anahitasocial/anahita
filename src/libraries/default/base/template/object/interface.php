<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 *
 * @link       http://www.Anahita.io
 */

/**
 * Template object interface.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
interface LibBaseTemplateObjectInterface extends AnConfigInterface
{
    /**
     * Return a template object name that uniquly identifieds a template object.
     *
     * @return string
     */
    public function getName();
}
