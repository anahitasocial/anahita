<?php


 /**
  * Enabable Behavior.
  *
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.GetAnahita.com
  */
 class ComBaseDomainBehaviorEnableable extends LibBaseDomainBehaviorEnableable
 {
     /**
     * {@inheritdoc}
     *
     * Only brings the entities that are enabled
     *
     * @param KCommandContext $context Context Parameter
     */
    protected function _beforeRepositoryFetch(KCommandContext $context)
    {
        $query = $context->query;
        $query->where('IF(@col(enabled)=FALSE,0,1)');
    }
 }
