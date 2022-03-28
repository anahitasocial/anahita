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
 * Authorizer Behavior.
 * 
 * An authorizer behavior provides an interface to ask an entity to authorize actions. The act of autherization is 
 * performed by the authorizer classes that are registered with the entity repository.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class LibBaseDomainBehaviorAuthorizer extends AnDomainBehaviorAbstract
{
    /**
      * Array of authorizers.
      * 
      * @var array
      */
     protected $_authorizers = array();

     /**
      * Constructor.
      *
      * @param AnConfig $config An optional AnConfig object with configuration options.
      */
     public function __construct(AnConfig $config)
     {
         parent::__construct($config);

         $authorizers = array_reverse(array_unique(AnConfig::unbox($config->authorizers)));

         foreach ($authorizers as $authorizer) {
             $this->addAuthorizer($authorizer);
         }
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
            'authorizers' => array($config->mixer->getIdentifier()->name),
        ));

        parent::_initialize($config);
    }

    /**
     * Adds an authorizer.
     *
     * @param LibBaseDomainAuthorizerAbstract $authorizer The authorizer object
     */
    public function addAuthorizer($authorizer)
    {
        if (! $authorizer instanceof LibBaseDomainAuthorizerAbstract) {
            if (is_string($authorizer) && strpos($authorizer, '.') === false) {
                //create identifier
                $identifier = clone $this->_repository->getIdentifier();
                $identifier->path = array('domain','authorizer');
                $identifier->name = $authorizer;
                register_default(array('identifier' => $identifier, 'prefix' => $this->_repository->getClone()));
            } else {
                $identifier = AnService::getIdentifier($authorizer);
            }

            $authorizer = $identifier;

            $authorizer = AnService::get($authorizer);
        }

        array_unshift($this->_authorizers, $authorizer);
    }

    /**
     * Authorize an action and return true or false.
     * 
     * @param string $action The action name
     * @param array  $config An array of options to pass to the authorizers
     * 
     * @return bool
     */
    public function authorize($action, $config = array())
    {
        if (is_string($config)) {
            $config = array($action => $config);
        }

        $config = AnConfig::unbox($config);
        $context = $this->_mixer->getRepository()->getCommandContext();

        $context->append($config)->append(array(
            'viewer' => get_viewer(),
            'mixer' => $this->_mixer,
        ));

        $authorizers = $this->_authorizers;
        $result = LibBaseDomainAuthorizerAbstract::AUTH_NOT_IMPLEMENTED;

        foreach ($authorizers as $authorizer) {
            $result = $authorizer->execute($action, $context);
            if ($result !== LibBaseDomainAuthorizerAbstract::AUTH_NOT_IMPLEMENTED) {
                break;
            }
        }

        $context->authorization = $result;

        $method = 'authorize'.ucfirst($action);

        if (method_exists($this->_mixer, $method)) {
            $ret = $this->_mixer->$method($context);

            if (! is_null($ret)) {
                $context->authorization = $ret;
            }
        }

        return $context->authorization;
    }
}
