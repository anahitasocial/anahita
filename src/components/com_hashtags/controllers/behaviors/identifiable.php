<?php

/**
 * Identifiable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComHashtagsControllerBehaviorIdentifiable extends ComBaseControllerBehaviorIdentifiable
{
    /**
     * (non-PHPdoc).
     *
     * @see ComBaseControllerBehaviorIdentifiable::fetchEntity()
     */
    public function fetchEntity(AnCommandContext $context)
    {
        if ($this->isDispatched() && $this->getRequest()->alias) {
            $this->setIdentifiableKey('alias');
        }

        return parent::fetchEntity($context);
    }
}
