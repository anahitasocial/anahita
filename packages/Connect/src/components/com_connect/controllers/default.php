<?php

/**
 * Default controller handles calls to the oauth handlers (facebook,twitter).
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComConnectControllerDefault extends ComBaseControllerResource
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
            'behaviors' => array('ownable'),
        ));

        parent::_initialize($config);
    }

    /**
     * Authorize a GET request.
     *
     * @return bool
     */
    public function canGet()
    {
        if (!$this->actor) {
            $this->actor = $this->getService('com:people.viewer');
        }

        if (!$this->actor) {
            return false;
        }

        if (!$this->actor->authorize('administration')) {
            return false;
        }

        $this->getService('repos:connect.session');

        $api = $this->actor->sessions->{$this->getIdentifier()->name};

        if (!$api) {
            return false;
        }

        $this->api = $api;
        
        return true;
    }

    /**
     * Return whether can delete or not.
     *
     * @return bool
     */
    public function canDelete()
    {
        return $this->canGet();
    }

    /**
     * Deletes a session.
     *
     * @param AnCommandContext $context
     */
    protected function _actionDelete(AnCommandContext $context)
    {
        if ($session = $this->actor->sessions->find(array('api' => $this->getIdentifier()->name))) {
            $session->delete()->save();
        }
    }

    /**
     * Dispatches a call to the oauth handler.
     *
     * @return
     */
    protected function _actionGet(AnCommandContext $context)
    {
        if ($context->request->getFormat() == 'html') {
            $context->response->setRedirect(route('format=json&option=com_connect&view='.$this->view));
            return;
        }

        if ($this->get) {
            $url = ltrim($this->get, '/');
            $data = KConfig::unbox($this->api->get($url));
            $data = json_encode($data);
        } else {
            $data = (array) $this->api->getUser();
        }

        $this->getView()->data($data);

        return parent::_actionGet($context);
    }
}
