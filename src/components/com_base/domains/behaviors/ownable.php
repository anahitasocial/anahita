<?php


 /**
  * An ownable node by an actor is a node that can have one main owner and multiple shared owner.
  *
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahita.io>
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.Anahita.io
  */
 class ComBaseDomainBehaviorOwnable extends AnDomainBehaviorAbstract
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
            'relationships' => array(
                'owner' => array(
                    'polymorphic' => true,
                    'required' => true,
                    'parent' => 'com:actors.domain.entity.actor',
                    'inverse' => true,
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Set the owner of the object. If multiple owners are passed then the first owner is the
     * primary owner and the rest just share the object with the owner.
     *
     * @param ComActorsDomainEntityActor $owner The owner object
     *
     * @return ComBaseDomainEntityNode Return the ownable object
     */
    public function setOwner($owner)
    {
        //multiple owners are passed
        $owner = AnConfig::unbox($owner);

        if (is_array($owner)) {
            deprecated('array as owner');
        }

        if (is_array($owner) && $this->isSharable()) {
            $owners = AnHelperArray::unique($owner);
            //remove the first owner as the primary owner
            $owner = array_shift($owners);
            //create an edge with the story for each secondary owner
            //$this->addOwner($owners);
        }
        $this->_mixer->set('owner', $owner);

        return $this;
    }
 }
