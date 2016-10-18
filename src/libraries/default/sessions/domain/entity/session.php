<?php

/**
 * Session Entity
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibSessionsDomainEntitySession extends AnDomainEntityDefault
{
    /**
    *   60 days in seconds = 60 * 24 * 3600
    *
    *   @var integer
    */
    const MAX_LIFETIME = 5184000;

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
            'resources' => array('sessions'),
            'attributes' => array(
                'id',
                'sessionId' => array(
                    'required' => true
                ),
                'username' => array(
                    'default' => ''
                ),
                'nodeId' => array(
                    'default' => 0
                ),
                'usertype' => array(
                    'default' => ComPeopleDomainEntityPerson::USERTYPE_GUEST,
                    'required' => true
                ),
                'guest' => array(
                    'default' => 1,
                    'required' => true
                ),
                'meta' => array(
                    'default' => '',
                ),
                'time' => array(
                    'default' => 0
                )
            )
        ));

        parent::_initialize($config);
    }

    /**
    * Updates time
    *
    * @return this
    */
    public function updateTime()
    {
        $this->time = time();
        return $this;
    }

    protected function _beforeEntityInsert(KCommandContext $context)
    {
        $this->time = time();
    }

    protected function _beforeEntityUpdate(KCommandContext $context)
    {
        if ($this->getModifiedData()) {
            $viewer = get_viewer();
            $this->setData(array(
                'time' => time(),
                'nodeId' => $viewer->id,
                'guest' => $viewer->guest(),
                'username' => $viewer->username,
                'usertype' => $viewer->usertype
            ));
        }
    }
}
