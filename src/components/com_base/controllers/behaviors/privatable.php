<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Privatable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseControllerBehaviorPrivatable extends AnControllerBehaviorAbstract
{
    /**
     * Set a privacy for a privatable entity.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionSetprivacy($context)
    {
        $data = $context->data;

        $names = AnConfig::unbox($data->privacy_name);

        settype($names, 'array');
        
        foreach ($names as $name) {
            $this->getItem()->setPermission($name, $data->$name);
        }
        
        $context->response->content = $this->_mixer->display();
    }

    /**
     * Authorize setting privacy.
     *
     * @return bool
     */
    public function canSetprivacy()
    {
        if ($this->getItem()) {
            return $this->getItem()->authorize('edit');
        }

        return false;
    }
}
