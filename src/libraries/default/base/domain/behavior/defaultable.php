<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * Defultable Behavior. 
 * 
 * Allows to set an entity as the default entity within a set of entities
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class LibBaseDomainBehaviorDefaultable extends AnDomainBehaviorAbstract
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
                'isDefault' => array(
                    'default' => false,
                ), ),
        ));

        parent::_initialize($config);
    }

    /**
     * Set the order before inserting.
     *
     * @param AnCommandContext $context
     */
    protected function _beforeEntityInsert(AnCommandContext $context)
    {
        //if the entity default is set to 
        //true then, set the previous default entity to false
        if ($this->_mixer->isDefault === true) {
            $query = $this->getScopedQuery($context->entity);
            $this->getRepository()->update(array('isDefault' => false), $query);
        }
    }

    /**
     * Reorder After Update.
     *
     * @param AnCommandContext $context
     */
    protected function _beforeEntityUpdate(AnCommandContext $context)
    {
        //if default has changed
        if ($this->_mixer->getModifiedData()->isDefault) {
            $is_default = $this->_mixer->isDefault === true;
            $query = $this->getScopedQuery($context->entity);
            //if it's true, then reset all existing to false
            if ($is_default) {
                $this->getRepository()->update(array('isDefault' => false), $query);
            } else {
                $query->id($this->id, '<>')->limit(1);
                $this->getRepository()->update(array('isDefault' => true), $query);
            }
        }
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
