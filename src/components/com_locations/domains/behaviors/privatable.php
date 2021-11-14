<?php

/**
 * Search Privateable
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComLocationsDomainBehaviorPrivatable extends ComTagsDomainBehaviorPrivatable
{
    /**
     * {@inheritdoc}
     */
    protected function _beforeQuerySelect(AnCommandContext $context)
    {
        if (AnService::has('com:people.viewer') && is_person(get_viewer()) && get_viewer()->admin()) {
            return;
        }

        $query = $context->query;

        $repository = $query->getRepository();

        $config = pick($query->privacy, new AnConfig());

        $config->append(array(
            'visible_to_leaders' => true,
            'viewer' => AnService::get('com:people.viewer'),
            'graph_check' => true,
        ));

        $query->getRepository()->addBehavior('ownable');

        //do a left join operation just in case an owner is missing
        $query->link('owner', array('type' => 'weak', 'bind_type' => false));

        $config->append(array(
            'use_access_column' => '@col(access)',
        ));

        $c1 = $this->buildCondition('@col(owner.id)', $config, '@col(owner.access)');
        $c2 = $this->buildCondition('@col(owner.id)', $config, $config->use_access_column);

        $where = "IF($c1, $c2, 0)";

        $query->where($where);
    }
}
