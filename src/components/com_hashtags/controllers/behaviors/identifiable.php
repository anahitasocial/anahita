<?php

/**
 * Identifiable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComHashtagsControllerBehaviorIdentifiable extends ComBaseControllerBehaviorIdentifiable
{
    /**
     * (non-PHPdoc).
     *
     * @see ComBaseControllerBehaviorIdentifiable::fetchEntity()
     */
    public function fetchEntity(KCommandContext $context)
    {
        if ($this->isDispatched() && $this->getRequest()->alias) {
            $this->setIdentifiableKey('alias');
        }

        return parent::fetchEntity($context);
    }
}
