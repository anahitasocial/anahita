<?php

/**
 * Enableable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComMediumDomainBehaviorEnableable extends LibBaseDomainBehaviorEnableable
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'attributes' => array(
                'enabled' => array(
                    'default' => true,
                ), ),
        ));

        parent::_initialize($config);
    }

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
        $query = $context->query;
        $repos = $query->getRepository();
        if ($repos->hasBehavior('ownable')) {
            if (!get_viewer()->admin()) {
                $ids = get_viewer()->administratingIds->toArray();
                $ids[] = get_viewer()->id;
                $ids = implode(',', $ids);
                $query->where("IF(@col(enabled) = FALSE, @col(owner.id) IN ($ids) ,1)");
            }
        }
    }
}
