<?php

/**
 * Medium Authorizer.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComMediumDomainAuthorizerComponent extends LibBaseDomainAuthorizerAbstract
{
    /**
     * Authorizes whether the viewer can pubilsh anything within this component or not.
     *
     * @param AnCommandContext $context
     *
     * @return bool
     */
    protected function _authorizeAction(AnCommandContext $context)
    {
        $method = '_authorize'.ucfirst($context->action);

        $ret = self::AUTH_NOT_IMPLEMENTED;

        if (method_exists($this, $method)) {
            $ret = $this->$method($context->resource);
        } elseif ($context->actor) {
            //check if it's enabled and assigned
            if ($this->_entity->isAssignable()) {
                $ret = $this->_entity->activeForActor($context->actor);
            }
        }

        return $ret;
    }
}
