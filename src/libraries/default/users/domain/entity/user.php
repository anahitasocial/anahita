<?php

/**
 * User object.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibUsersDomainEntityUser extends AnDomainEntityDefault
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
            'resources' => array('users'),
            'attributes' => array(
                'params' => array('required' => false, 'default' => ''),
                'activation' => array('required' => false, 'default' => ''),
            ),
            'auto_generate' => true,
        ));

        return parent::_initialize($config);
    }

    /**
     * Automatically sets the activation token for the user.
     *
     * @return LibUsersDomainEntityUser
     */
    public function requiresActivation()
    {
        jimport('joomla.user.helper');
        $token = JUtility::getHash(JUserHelper::genRandomPassword());
        $salt = JUserHelper::getSalt('ssha');
        $hashedToken = sha1($token.$salt).':'.$salt;
        $this->activation = $hashedToken;

        return $this;
    }
}
