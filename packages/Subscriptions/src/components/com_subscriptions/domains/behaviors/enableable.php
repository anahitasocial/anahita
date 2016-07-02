<?php

/**
 * Enableable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsDomainBehaviorEnableable extends LibBaseDomainBehaviorEnableable
{
    /**
     * {@inheritdoc}
     *
     * Only brings the media that are enabled or disabled but the viewer or one
     * of the actor they are administrating are the owner
     *
     * @param KCommandContext $context Context Parameter
     */
    protected function _beforeRepositoryFetch(KCommandContext $context)
    {
        if (!get_viewer()->admin()) {
            $query = $context->query;
            $query->where('IF(@col(enabled)=FALSE,0,1)');
        }
    }
}
