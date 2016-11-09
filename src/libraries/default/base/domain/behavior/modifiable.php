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
 * @link       http://www.GetAnahita.com
 */

/**
 * Modifiable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseDomainBehaviorModifiable extends AnDomainBehaviorAbstract
{
    /**
     * Modiable Properties.
     *
     * @var array
     */
    protected $_modifiable_properties = array();

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_modifiable_properties = KConfig::unbox($config->modifiable_properties);
    }

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
            'modifiable_properties' => array(),
            'attributes' => array(
                'creationTime' => array(
                    'column' => 'created_on',
                    'default' => 'date',
                    'required' => true
                ),
                'updateTime' => array('column' => 'modified_on', 'default' => 'date'),
            ),
            'relationships' => array(
                'author' => array('parent' => 'com:actors.domain.entity.actor', 'child_column' => 'created_by'),
                'editor' => array('parent' => 'com:actors.domain.entity.actor', 'child_column' => 'modified_by'),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Executes the command after.instantiate.
     *
     * @param KConfig $config Configuration parameter
     */
    protected function _afterEntityInstantiate(KConfig $config)
    {
        if (KService::has('com:people.viewer')) {
            $config->data->append(array(
                'author' => KService::get('com:people.viewer'),
            ));
        }
    }

    /**
     * Set author of a node. By setting an author the editor is also set to the author. The creationTime property is also updated.
     *
     * @param ComPeopleDomainEntityPerson $author The author of the entity
     */
    public function setAuthor($author)
    {
        $this->set('author',  $author);
        $this->creationTime = AnDomainAttributeDate::getInstance();
        $this->editor = $author;
    }

    /**
     * Set editr of a node. It also updates the updateTime property.
     *
     * @param ComPeopleDomainEntityPerson $editor An editor
     */
    public function setEditor($editor)
    {
        $this->set('editor', $editor);
        $this->updateTime = AnDomainAttributeDate::getInstance();
    }

    /**
     * Timestamping a node.
     */
    public function timestamp()
    {
        $this->updateTime = AnDomainAttributeDate::getInstance();

        if (!isset($this->creationTime)) {
            $this->creationTime = AnDomainAttributeDate::getInstance();
        }
    }

    /**
     * Before Update timestamp modified on and modifier.
     *
     * @param KCommandContext $context Context parameter
     */
    protected function _beforeEntityUpdate(KCommandContext $context)
    {
        $entity = $context->entity;
        $modified = array_keys(KConfig::unbox($entity->getModifiedData()));
        $modified = count(array_intersect($this->_modifiable_properties, $modified)) > 0;

        if ($modified && KService::has('com:people.viewer')) {
            $entity->editor = get_viewer();
        }
    }
}
