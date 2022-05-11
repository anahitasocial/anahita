<?php

/**
* Verifiable Behavior.
*
* @category   Anahita
*
* @author     Rastin Mehr <rastin@anahita.io>
* @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
*
* @link       http://www.Anahita.io
*/
class ComBaseDomainBehaviorVerifiable extends AnDomainBehaviorAbstract
{
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
            'attributes' => array(
                'verified' => array(
                    'default' => 0,
                    'write_access' => 'private',
                ),
              ),
        ));

        parent::_initialize($config);
    }

    /**
     * Set verified to true
     */
    public function verify()
    {
        $this->verified = 1;

        return $this;
    }

    /**
     * Set verified to false
     */
    public function unverify()
    {
        $this->verified = 0;

        return $this;
    }
}
