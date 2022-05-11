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
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * Orderable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class LibBaseDomainBehaviorOrderable extends AnDomainBehaviorAbstract
{
    /**
     * A property whose value can be used as scope.
     *
     * @var array
     */
    protected $_scopes;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_scopes = $config['scopes'];
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'scopes' => array(),
            'attributes' => array(
                'ordering' => array('default' => 0),
            ),
            'aliases' => array(
                'order' => 'ordering',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Before Update.
     *
     * @param AnCommandContext $context
     */
    protected function _beforeEntityUpdate(AnCommandContext $context)
    {
        if ($this->getModifiedData()->ordering) {
            $store = $this->getRepository()->getStore();
            $query = $this->getScopedQuery($context->entity);
            $change = $this->getModifiedData()->ordering;

            if ($change->new - $change->old < 0) {
                $query->update('@col(ordering) = @col(ordering) + 1');
                $query->where('ordering', '>=',  $change->new)->where('ordering', '<',   $change->old);
            } else {
                $query->update('@col(ordering) = @col(ordering) - 1');
                $query->where('ordering', '>',   $change->old)->where('ordering', '<=',  $change->new);
            }

            $store->execute($query);
        }
    }

    /**
     * Reorders all the entities.
     */
    public function reorder()
    {
        $store = $this->getRepository()->getStore();
        $query = $this->getScopedQuery($this->_mixer);
        $store->execute('SET @order = 0');
        $query->update('@col(ordering) = (@order := @order + 1)')->order('ordering', 'ASC');
        $store->execute($query);
    }

    /**
     * Set the order before inserting.
     *
     * @param AnCommandContext $context
     */
    protected function _beforeEntityInsert(AnCommandContext $context)
    {
        $max = $this->getScopedQuery($context->entity)->fetchValue('MAX(@col(ordering))');
        $this->ordering = $max + 1;
    }

    /**
     * Reorder After Update.
     *
     * @param AnCommandContext $context
     */
    protected function _afterEntityUpdate(AnCommandContext $context)
    {
        if ($this->getModifiedData()->ordering) {
            $this->reorder();
        }
    }

    /**
     * Reorder After Delete.
     *
     * @param AnCommandContext $context
     */
    protected function _afterEntityDelete(AnCommandContext $context)
    {
        $this->reorder();
    }

    /**
     * Return the query after applying the scope.
     *
     * @param AnDomainEntityAbstract $entity The  entity
     *
     * @return AnDomainQuery
     */
    public function getScopedQuery($entity)
    {
        $query = $this->getRepository()->getQuery();

        foreach ($this->_scopes as $key => $value) {
            if (is_numeric($key)) {
                $key = $value;
                $value = $entity->$key;
            }

            $query->where($key, '=', $value);
        }

        return $query;
    }
}
