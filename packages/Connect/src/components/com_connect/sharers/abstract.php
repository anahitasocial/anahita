<?php

/**
 * Connect abstract sharer. It uses a oauth session to share an object across a remote
 * service.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class ComConnectSharerAbstract extends KObject implements ComSharesSharerInterface
{
    /**
     * An authenticated oauth session.
     *
     * @var ComConnectOauthServiceAbstract
     */
    protected $_session;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_session = $config->session;

        if (!$this->_session instanceof ComConnectOauthServiceAbstract) {
            throw new InvalidArgumentException('Session must be an intance of ComConnectOauthServiceAbstract');
        }
    }

    /**
     * Return whether a boolean.
     *
     * @param ComSharesSharerRequest $request The share request
     *
     * @return bool Return whether the share was succesful or not
     */
    public function canShareRequest(ComSharesSharerRequest $request)
    {
        $object = $request->object;

        return $object->access == 'public' && !$this->_session->isReadOnly();
    }

    /**
     * Shares a request.
     *
     * @param ComSharesSharerRequest $request The share request
     *
     * @return bool Return whether the share was succesful or not
     */
    public function shareRequest(ComSharesSharerRequest $request)
    {
        $ret = false;

        if ($this->canShareRequest($request)) {
            $this->_session->postUpdate($request->object->body);
        }
    }
}
