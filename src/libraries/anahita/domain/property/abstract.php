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
 * Abstract Domain Entity Property. This is the base class for Attribute or Relationship
 * properties of an entity.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class AnDomainPropertyAbstract
{
    /**
     * Stores the clonable property instances.
     *
     * @var array
     */
    protected static $_instances = array();

    /**
     * Creates and initialize a property. The instantiation happens through cloning rather
     * then creating a new instance.
     *
     * @param string  $property The property type
     * @param KConfig $config   The property configuration
     *
     * @return AnDomainPropertyAbstract
     */
    public static function getInstance($property, KConfig $config)
    {
        $description = $config['description'];
        $name = $config['name'];

        if (!$name || !$description) {
            throw new AnDomainPropertyException('name [string] or desription [AnDomainDescriptionAbstract] options are missing');
        }

        if ($description->getProperty($name)) {
            $instance = $description->getProperty($name);
        } else {
            if (!isset(self::$_instances[$property])) {
                $classname = 'AnDomain'.AnInflector::camelize($property);
                self::$_instances[$property] = new $classname();
            }
            $instance = clone self::$_instances[$property];
        }

        $instance->setConfig($config);

        $description->setProperty($instance);

        return $instance;
    }

    /**
     * Name of the property.
     *
     * @var string
     */
    protected $_name;

    /**
     * Specified a property is readonly and can't be set.
     *
     * @var bool
     */
    protected $_write_access;

    /**
     * Specifies a properrty read access. It can be PUBLIC or PROTECTED. if set to protected then
     * when it won't be retrieved when $entity received $entity->getData(). By default all properties
     * are protected.
     *
     * @var bool
     */
    protected $_read_access;

    /**
     * If set to true, the property can not have a null or empty value.
     *
     * @var int
     */
    protected $_required;

    /**
     * Boolean value if a property is unique or not.
     *
     * @var bool
     */
    protected $_unique;

    /**
     * Property original configuration. Having this prevents from recreating an existing
     * property.
     *
     * @var KConfig
     */
    private $__config;

    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    final private function __construct()
    {
        //private
    }

    /**
     * Configures a property. Property are cloned so using this method, it's possibled to
     * re-configured a cloned property.
     *
     * @param KConfig $config Property Configuration
     */
    public function setConfig(KConfig $config)
    {
        //if the property configuration has been set
        //then don't allow it to change
        if (isset($this->__config)) {
            $this->__config->append($config);
            foreach ($this->__config as $key => $value) {
                $config[$key] = $this->__config[$key];
            }
        } else {
            $this->__config = $config;
        }

        $this->_name = $config->name;

        $this->_initialize($config);

        $this->_unique = $config->unique;
        $this->_required = $config->required;
        $this->_write_access = (int) $config->write;
        $this->_read_access = (int) $config->read;
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        //by default every property is write protected
        //meaning it's not possibl to do mass assignement
        $config->append(array(
            'unique' => false,
            'required' => false,
            'write' => AnDomain::ACCESS_PUBLIC,
            'read' => AnDomain::ACCESS_PROTECTED,
        ));

        if (is_string($config->write)) {

            switch ($config->write) {

                case 'private' :
                    $config->write = AnDomain::ACCESS_PRIVATE;
                    break;

                case 'protected' :
                    $config->write = AnDomain::ACCESS_PROTECTED;
                    break;

                case 'public' :
                    $config->write = AnDomain::ACCESS_PUBLIC;
                    break;
            }
        }

        if (is_string($config->read)) {

            switch ($config->read) {

                case 'private' :
                    $config->read = AnDomain::ACCESS_PRIVATE;
                    break;

                case 'protected' :
                    $config->read = AnDomain::ACCESS_PROTECTED;
                    break;

                case 'public' :
                    $config->read = AnDomain::ACCESS_PUBLIC;
                    break;
            }
        }
    }

    /**
     * Name of the property.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set write access.
     *
     * @param bool $access The proeprty read access
     *
     * @return AnDomainPropertyAbstract
     */
    public function setWriteAccess($access)
    {
        $this->_write_access = (int) $access;

        return $this;
    }

    /**
     * Set read access.
     *
     * @param bool $access The proeprty read access
     *
     * @return AnDomainPropertyAbstract
     */
    public function setReadAccess($access)
    {
        $this->_read_access = (int) $access;

        return $this;
    }

    /**
     * Return the property read access.
     *
     * @return int
     */
    public function getReadAccess()
    {
        return $this->_read_access;
    }

    /**
     * Return property access.
     *
     * @return bool
     */
    public function getWriteAccess()
    {
        return $this->_write_access;
    }

    /**
     * Set true/false if a property is required.
     *
     * @param bool $value A boolean value
     */
    public function setRequired($value)
    {
        $this->_required = $value;
    }

    /**
     * Return whehter a property is required or not.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->_required;
    }

    /**
     * Set true/false if a property is unique.
     *
     * @param bool $value A boolean value
     */
    public function setUnique($value)
    {
        $this->_unique = $value;
    }

    /**
     * Return if a property is unique.
     *
     * @return bool
     */
    public function isUnique()
    {
        return $this->_unique;
    }

    /**
     * Return if a property is uniquable.
     *
     * @return bool
     */
    public function isSerializable()
    {
        return $this instanceof AnDomainPropertySerializable;
    }

    /**
     * Return if a property is a attribute.
     *
     * @return bool
     */
    public function isAttribute()
    {
        return $this instanceof AnDomainAttributeProperty;
    }

    /**
     * Return if a property is a relationship.
     *
     * @return bool
     */
    public function isRelationship()
    {
        return $this instanceof AnDomainRelationshipProperty;
    }

    /**
     * Provides a test to see if a property is materializable given data.
     *
     * @param array $data
     *
     * @return bool
     */
    abstract public function isMaterializable(array $data);

    /**
     * Materilize a property from the raw data. If the entity object has been
     * already been initialized it will be passed to the function. If a property is
     * unique it will not be passed the entity.
     *
     * @param array                  $data
     * @param AnDomainEntityAbstract $entity
     */
    abstract public function materialize(array $data, $entity);

    /**
     * Unset the initiale configuration when a property is cloned.
     */
    public function __clone()
    {
        unset($this->__config);
    }
}
