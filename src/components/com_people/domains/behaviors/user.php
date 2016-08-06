<?php

/**
 * User Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleDomainBehaviorUser extends AnDomainBehaviorAbstract
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
                'userId' => array(
                    'column' => 'person_userid',
                    'key' => true,
                    'type' => 'integer',
                    'default' => mt_rand(),
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * before creating the person node, create the user object.
     *
     * @return bool
     */
    protected function _beforeEntityInsert(KCommandContext $context)
    {
        $viewer = get_viewer();
        $firstUser = !(bool) $this->getService('repos://site/users')
                                  ->getQuery(true)
                                  ->fetchValue('id');

        jimport('joomla.user.helper');
        $user = clone JFactory::getUser();

        $user->set('id', 0);
        $user->set('name', $this->name);
        $user->set('username', $this->username);
        $user->set('email', $this->email);

        if (!$this->getPassword()) {
            $this->setPassword(JUserHelper::genRandomPassword(32));
        }

        if ($this->getPassword()) {
            $user->set('password', $this->getPassword(true));
            $user->set('password_clear', $this->getPassword());
        }

        $date = &JFactory::getDate();
        $user->set('registerDate', $date->toMySQL());
        $user->set('lastvisitDate', '0000-00-00 00:00:00');

        // if this is the first user being added or
        // (viewer is a super admin and she is adding another super admin)
        if (
            $firstUser ||
            (
                $viewer->superadmin() &&
                $this->usertype == ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR
            )
        ) {
            $user->set('usertype', ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR);
        } elseif (
            $viewer->admin() &&
            $this->usertype == ComPeopleDomainEntityPerson::USERTYPE_ADMINISTRATOR
        ) {
            $user->set('usertype', ComPeopleDomainEntityPerson::USERTYPE_ADMINISTRATOR);
        } else {
            $user->set('usertype', ComPeopleDomainEntityPerson::USERTYPE_REGISTERED);
        }

        //create an activation token
        //@todo we need a global token generator to handle creation and destruction of tokens
        $user->set('activation', JUtility::getHash(JUserHelper::genRandomPassword()));
        $user->set('block', '1');

        if (!$user->save()) {
            throw new RuntimeException('Unexpected error when saving user');

            return false;
        }

        $this->userId = $user->id;
        $this->usertype = $user->usertype;
        $this->enabled = ($user->block) ? 0 : 1;

        return true;
    }

    /**
     * Update the user object before updating the person node.
     *
     * @return bool
     */
    protected function _afterEntityUpdate(KCommandContext $context)
    {
        $data = $context->data;
        jimport('joomla.user.helper');
        $viewer = get_viewer();
        $user = clone JFactory::getUser($this->userId);

        if ($this->getModifiedData()->name) {
            $user->set('name', $this->name);
        }

        if ($this->getModifiedData()->username) {
            $user->set('username', $this->username);
        }

        if ($this->getModifiedData()->email) {
            $user->set('email', $this->email);
        }

        if ($this->getModifiedData()->enabled) {
            $user->set('block', !$this->enabled);
        }

        if ($this->getModifiedData()->usertype) {
            $user->set('usertype', $this->usertype);
        }

        if ($this->getModifiedData()->enabled) {
            $user->set('block', !$this->enabled);
        }

        if ($this->getPassword()) {
            $user->set('password', $this->getPassword(true));
            $user->set('password_clear', $this->getPassword());
        }

        if (@$this->params->language) {
            $user->_params->set('language', $this->params->language);
        }

        if (!$user->save()) {
            throw new RuntimeException('Unexpected error when saving user');

            return false;
        }

        return true;
    }

    /**
     * Return the user object of the person.
     *
     * @return LibUsersDomainEntityUser
     */
    public function getUserObject()
    {
        return $this->getService('repos://site/users.user')
                     ->fetch(array('id' => $this->userId));
    }

    /**
    *   Return last visit date from the user object
    *
    *   @return AnDomainAttributeDate object
    */
    public function getLastLoginDate()
    {
        $user = $this->getUserObject();
        return $user->get('lastvisitDate');
    }
}
