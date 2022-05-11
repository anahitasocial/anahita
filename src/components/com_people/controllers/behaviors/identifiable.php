<?php

/**
 * Identifiable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComPeopleControllerBehaviorIdentifiable extends ComBaseControllerBehaviorIdentifiable
{
    /**
     * (non-PHPdoc).
     *
     * @see ComBaseControllerBehaviorIdentifiable::fetchEntity()
     */
    public function fetchEntity(AnCommandContext $context)
    {
        if ($this->isDispatched()) {
            $username = $this->getRequest()->username;
            if ($username && $this->getRequest()->get('layout') != 'add') {
                $this->setIdentifiableKey('username');
            }
        }

        return parent::fetchEntity($context);
    }
}
