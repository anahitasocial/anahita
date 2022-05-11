<?php

/**
 * Session Entity
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class LibSessionsDomainEntitySession extends AnDomainEntityDefault
{
    /**
    *   60 days in seconds = 60day * 24hour * 60min * 60sec
    *
    *   @var integer
    */
    const MAX_LIFETIME = 5184000;

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

    protected function _beforeEntityInsert(AnCommandContext $context)
    {
        $this->time = time();
    }

    protected function _beforeEntityUpdate(AnCommandContext $context)
    {
        if ($this->getModifiedData()) {
            $viewer = AnService::get('com:people.viewer');
            $this->setData(array(
                'time' => time(),
                'nodeId' => (int) $viewer->id,
                'guest' => (int) $viewer->guest(),
                'username' => $viewer->username,
                'usertype' => $viewer->usertype
            ));
        }
    }
}
