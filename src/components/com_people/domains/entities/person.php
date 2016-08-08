<?php

/**
 * Person object. It's the main actor node that represents the social network users. A person can added
 * applications to their profile.
 *
 * Here's how to get a person object, set a property and save
 * <code>
 * //fetches a peron with $id
 * $person = KService::get('repos://site/people.person')->fetch($id);
 * $person->name = 'Doctor Who';
 * $person->save();
 * </code>
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
final class ComPeopleDomainEntityPerson extends ComActorsDomainEntityActor
{
    /*
     * Clear string passwrod.
     *
     * @var string
     */
    protected $_password;

    /*
    * Allowed user types array
    */
    protected $_allowed_user_types;

    /*
     * Mention regex pattern
     */
    const PATTERN_MENTION = '/@([A-Za-z][A-Za-z0-9_-]{3,})/';

    /*
     * User types
     */
    const USERTYPE_GUEST = 'guest';
    const USERTYPE_REGISTERED = 'registered';
    const USERTYPE_ADMINISTRATOR = 'administrator';
    const USERTYPE_SUPER_ADMINISTRATOR = 'super-administrator';

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
            'resources' => array('people_people'),
            'attributes' => array(
                'administratingIds' => array(
                    'type' => 'set',
                    'default' => 'set'
                ),
                'alias' => array(
                    'key' => true,
                    'format' => 'username'
                ),
                'username' => array(
                    'key' => true,
                    'format' => 'username',
                    'required' => true,
                    'read' => AnDomain::ACCESS_PUBLIC
                ),
                'password' => array(
                    'required' => true
                ),
                'usertype' => array(
                    'default' => self::USERTYPE_REGISTERED,
                    'read' => AnDomain::ACCESS_PUBLIC
                ),
                'email' => array(
                    'key' => true,
                    'format' => 'email',
                    'read' => AnDomain::ACCESS_PUBLIC
                ),
                'gender',
                'lastVisitDate' => array(
                    'default' => 'data'
                )
            ),
            'aliases' => array(
                'registrationDate' => 'creationTime'
            ),
            'behaviors' => to_hash(array(
                //@todo if viewer is admin, then make email searchable too
                'describable' => array('searchable_properties' => array('username')),
                'administrator',
                'notifiable',
                'leadable'
            )),
        ));

        $config->behaviors->append(array('followable' => array('subscribe_after_follow' => false)));

        parent::_initialize($config);

        AnHelperArray::unsetValues($config->behaviors, array('administrable'));

        $this->_password = array(
            'clear' => null,
            'hashed' => null,
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterEntityInstantiate(KConfig $config)
    {
        $config->append(array('data' => array(
            'author' => $this,
            'component' => 'com_people',
        )));

        parent::_afterEntityInstantiate($config);
    }

    /**
     * Set the name, given name and family name of the person.
     *
     * @param string $name The name of the person
     */
    public function setName($name)
    {
        $familyName = $givenName = '';

        if (strpos($name, ' ')) {
            list($givenName, $familyName) = explode(' ', $name, 2);
        } else {
            $givenName = $name;
        }

        $this->set('givenName', $givenName);
        $this->set('familyName', $familyName);
        $this->set('name', $name);
    }

    /**
     * Set the name, given name and family name of the person.
     *
     * @param string $name The name of the person
     */
    public function setFamilyName($name)
    {
        $this->set('familyName', $name);
        $this->set('name', $this->givenName.' '.$this->familyName);
    }

    /**
     * Set the name, given name and family name of the person.
     *
     * @param string $name The name of the person
     */
    public function setGivenName($name)
    {
        $this->set('givenName', $name);
        $this->set('name', $this->givenName.' '.$this->familyName);
    }

    /**
     * Captures the password value when password is set through
     * magic methods.
     *
     * {@inheritdoc}
     */
    public function __set($key, $value)
    {
        if ($key == 'username') {
            $this->set('alias', $value);
        }

        if ($key == 'password') {
            jimport('joomla.user.helper');
            $salt = JUserHelper::genRandomPassword(32);
            $crypt = JUserHelper::getCryptedPassword($value, $salt);
            $this->password = $crypt.':'.$salt;
        }

        return parent::__set($key, $value);
    }

    /**
     * Return a person URL.
     *
     * @param bool $use_username A flag whether to use the username in the URL or not
     *
     * @return string
     */
    public function getURL($use_username = true)
    {
        $url = 'option=com_people&view=person&id='.$this->id;

        if ($use_username) {
            $url .= '&uniqueAlias='.$this->alias;
        }

        return $url;
    }

    /**
     * Return whether this person is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return $this->usertype === self::USERTYPE_GUEST;
    }

    /**
     * Return if the person user role is Administrator or Super Administrator.
     *
     * @return bool
     */
    public function admin()
    {
        return $this->usertype === self::USERTYPE_ADMINISTRATOR ||
               $this->usertype === self::USERTYPE_SUPER_ADMINISTRATOR;
    }

    /**
     * return true if the person's role is super admin.
     *
     * @return bool
     */
    public function superadmin()
    {
        return $this->usertype === self::USERTYPE_SUPER_ADMINISTRATOR;
    }

    /**
     * Before Update timestamp modified on and modifier.
     *
     * @param KCommandContext $context Context parameter
     */
    protected function _beforeEntityUpdate(KCommandContext $context)
    {
        $this->lastVisitDate = AnDomainAttributeDate::getInstance();
    }
}
